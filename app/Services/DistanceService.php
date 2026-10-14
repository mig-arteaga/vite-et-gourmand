<?php

namespace App\Services;

class DistanceService {
    private const ORIGIN_ADDRESS = '3 Place Notre Dame, 38000 Grenoble';

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

        $apiKey = getenv('GOOGLE_ROUTES_API_KEY');

        if (!$apiKey) {
            throw new \RuntimeException('Clé API Google non configurée');
        }

        $query = trim("$address, $zipcode $city, France");

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?'
            . http_build_query([
                'address' => $query,
                'components' => 'country:FR',
                'key' => $apiKey
            ]);

        $ch = curl_init($url);

        if ($ch === false) {
            throw new \RuntimeException('Impossible d’initialiser cURL');
        }

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \RuntimeException(
                'Erreur Google Geocoding : ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException(
                'Google Geocoding a retourné une erreur HTTP ' . $httpCode
            );
        }

        $result = json_decode($response, true);

        if (($result['status'] ?? '') === 'ZERO_RESULTS') {
            return false;
        }

        if (($result['status'] ?? '') !== 'OK') {
            throw new \RuntimeException(
                'Erreur Google Geocoding : '
                . ($result['status'] ?? 'inconnue')
                . ' - '
                . ($result['error_message'] ?? 'Aucun détail')
            );
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
        // $zipcode === '' ||
        mb_strlen($address) > 200 ||
        mb_strlen($city) > 100 ||
        mb_strlen($zipcode) > 20
    ) {
        throw new \InvalidArgumentException(
            'Adresse de livraison invalide'
        );
    }

        $destination = trim("$address, $zipcode $city");

        if (!$this->isAddressValid($address, $city, $zipcode)) {
            throw new \InvalidArgumentException(
                'Adresse de livraison introuvable ou invalide'
            );
        }

        $data = [
            'origin' => [
                'address' => self::ORIGIN_ADDRESS
            ],
            'destination' => [
                'address' => $destination
            ],
            'travelMode' => 'DRIVE'
        ];

        $apiKey = getenv('GOOGLE_ROUTES_API_KEY');

        if (!$apiKey) {
            throw new \RuntimeException('Clé API Google Routes non configurée');
        }

        $headers = [
            'Content-Type: application/json',
            "X-Goog-Api-Key: $apiKey",
            'X-Goog-FieldMask: routes.distanceMeters'
        ];

        $ch = curl_init('https://routes.googleapis.com/directions/v2:computeRoutes');

        if ($ch === false) {
            throw new \RuntimeException('Impossible d’initialiser cURL');
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

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);

            throw new \RuntimeException(
                'Erreur Google Routes : ' . $error
            );
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException(
                'Google Routes a retourné une erreur HTTP ' . $httpCode
            );
        }

        $result = json_decode($response, true);

        if (!isset($result['routes'][0]['distanceMeters'])) {
            throw new \RuntimeException(
                'Distance introuvable dans la réponse Google Routes'
            );
        }

        $distanceMeters = $result['routes'][0]['distanceMeters'];

        // Livraison à Grenoble : aucun frais kilométrique
        if (mb_strtolower($city) === 'grenoble') {
            return 0.0;
        }

        return (float) ceil($distanceMeters / 1000);
    }
}