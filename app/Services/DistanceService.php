<?php

declare(strict_types=1);

namespace App\Services;

class DistanceService {
    private const ORIGIN_ADDRESS = '3 Place Notre Dame, 38000 Grenoble';
    private const ORIGIN_LAT = 45.192817;
    private const ORIGIN_LON = 5.731398;

    public function isAddressValid(
        string $address,
        string $city,
        string $zipcode = ''
    ): bool {
        $address = trim($address);
        $city = trim($city);
        $zipcode = trim($zipcode);

        if ($address === '' || $city === '') {
            return false;
        }

        if ($this->checkGoogleGeocode($address, $city, $zipcode)) {
            return true;
        }

        return $this->geocodeWithBan($address, $city, $zipcode) !== null;
    }

    public function getDistanceKm(
        string $address,
        string $city,
        string $zipcode
    ): float {
        $address = trim($address);
        $city = trim($city);
        $zipcode = trim($zipcode);

        if (
            $address === '' ||
            $city === '' ||
            mb_strlen($address) > 200 ||
            mb_strlen($city) > 100 ||
            mb_strlen($zipcode) > 20
        ) {
            throw new \InvalidArgumentException(
                'Adresse de livraison invalide'
            );
        }

        // 1. Tentative via Google Geocoding + Google Routes si la clé est active et facturable
        $googleDistance = $this->tryGoogleDistanceKm($address, $city, $zipcode);
        if ($googleDistance !== null) {
            return $googleDistance;
        }

        // 2. Fallback via l'API Adresse officielle de l'État français (BAN) + OSRM
        $coords = $this->geocodeWithBan($address, $city, $zipcode);
        if ($coords === null) {
            throw new \InvalidArgumentException(
                'Adresse de livraison introuvable ou invalide'
            );
        }

        // Livraison à Grenoble : aucun frais kilométrique
        if (mb_strtolower($city) === 'grenoble') {
            return 0.0;
        }

        $distanceMeters = $this->computeOsrmDistanceMeters(
            self::ORIGIN_LAT,
            self::ORIGIN_LON,
            $coords['lat'],
            $coords['lon']
        );

        if ($distanceMeters === null) {
            // Secours par formule de Haversine majorée d'un coefficient routier (1.3)
            $distanceMeters = $this->computeHaversineMeters(
                self::ORIGIN_LAT,
                self::ORIGIN_LON,
                $coords['lat'],
                $coords['lon']
            ) * 1.3;
        }

        return (float) ceil($distanceMeters / 1000);
    }

    private function checkGoogleGeocode(
        string $address,
        string $city,
        string $zipcode
    ): bool {
        $apiKey = getenv('GOOGLE_ROUTES_API_KEY');
        if (!$apiKey) {
            return false;
        }

        $query = trim("$address, $zipcode $city, France");
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?'
            . http_build_query([
                'address' => $query,
                'components' => 'country:FR',
                'key' => $apiKey
            ]);

        $result = $this->fetchJson($url);
        if (!is_array($result) || ($result['status'] ?? '') !== 'OK') {
            return false;
        }

        foreach ($result['results'] ?? [] as $location) {
            if (
                !($location['partial_match'] ?? false)
                && in_array('street_address', $location['types'] ?? [], true)
                && ($location['geometry']['location_type'] ?? '') === 'ROOFTOP'
            ) {
                return true;
            }
        }

        return false;
    }

    private function tryGoogleDistanceKm(
        string $address,
        string $city,
        string $zipcode
    ): ?float {
        $apiKey = getenv('GOOGLE_ROUTES_API_KEY');
        if (!$apiKey || !$this->checkGoogleGeocode($address, $city, $zipcode)) {
            return null;
        }

        $destination = trim("$address, $zipcode $city");
        $data = [
            'origin' => [
                'address' => self::ORIGIN_ADDRESS
            ],
            'destination' => [
                'address' => $destination
            ],
            'travelMode' => 'DRIVE'
        ];

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: $apiKey",
            'X-Goog-FieldMask: routes.distanceMeters'
        ];

        $ch = curl_init('https://routes.googleapis.com/directions/v2:computeRoutes');
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $result = json_decode($response, true);
        if (!isset($result['routes'][0]['distanceMeters'])) {
            return null;
        }

        if (mb_strtolower($city) === 'grenoble') {
            return 0.0;
        }

        return (float) ceil(((float) $result['routes'][0]['distanceMeters']) / 1000);
    }

    /**
     * Géocodage via la Base Adresse Nationale (api-adresse.data.gouv.fr).
     *
     * @return array{lat: float, lon: float}|null
     */
    private function geocodeWithBan(
        string $address,
        string $city,
        string $zipcode
    ): ?array {
        $query = trim("$address $zipcode $city");
        $params = [
            'q' => $query,
            'limit' => 5
        ];

        if (preg_match('/^\d{5}$/', $zipcode)) {
            $params['postcode'] = $zipcode;
        }

        $url = 'https://api-adresse.data.gouv.fr/search/?' . http_build_query($params);
        $result = $this->fetchJson($url);

        if (!is_array($result) || empty($result['features'])) {
            return null;
        }

        $normalizedTargetCity = $this->normalizeString($city);

        foreach ($result['features'] as $feature) {
            $score = (float) ($feature['properties']['score'] ?? 0);
            $featureCity = $this->normalizeString((string) ($feature['properties']['city'] ?? ''));
            $coords = $feature['geometry']['coordinates'] ?? null;

            if (
                $score >= 0.45 &&
                is_array($coords) &&
                count($coords) >= 2 &&
                ($featureCity === $normalizedTargetCity || str_contains($featureCity, $normalizedTargetCity) || str_contains($normalizedTargetCity, $featureCity))
            ) {
                return [
                    'lon' => (float) $coords[0],
                    'lat' => (float) $coords[1]
                ];
            }
        }

        return null;
    }

    private function computeOsrmDistanceMeters(
        float $originLat,
        float $originLon,
        float $destLat,
        float $destLon
    ): ?float {
        $url = sprintf(
            'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F?overview=false',
            $originLon,
            $originLat,
            $destLon,
            $destLat
        );

        $result = $this->fetchJson($url);
        if (
            !is_array($result) ||
            ($result['code'] ?? '') !== 'Ok' ||
            !isset($result['routes'][0]['distance'])
        ) {
            return null;
        }

        return (float) $result['routes'][0]['distance'];
    }

    private function computeHaversineMeters(
        float $lat1,
        float $lon1,
        float $lat2,
        float $lon2
    ): float {
        $earthRadius = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function normalizeString(string $value): string {
        $value = mb_strtolower(trim($value), 'UTF-8');
        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if ($transliterated !== false) {
            $value = $transliterated;
        }

        return preg_replace('/[^a-z0-9]+/', '', $value) ?? $value;
    }

    private function fetchJson(string $url): ?array {
        $ch = curl_init($url);
        if ($ch === false) {
            return null;
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'ViteEtGourmand/1.0'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $httpCode !== 200) {
            return null;
        }

        $decoded = json_decode($response, true);

        return is_array($decoded) ? $decoded : null;
    }
}
