<?php

namespace App\Services;

class DistanceService {
    private const ORIGIN_ADDRESS = '3 Place Notre Dame, 38000 Grenoble';

    public function getDistanceKm(
        string $address,
        string $city,
        string $zipcode
    ): float {
        if (mb_strtolower(trim($city)) === 'grenoble') {
            return 0.0;
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

        return (float) ceil($distanceMeters / 1000);
    }
}