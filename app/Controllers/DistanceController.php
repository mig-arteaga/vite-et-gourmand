<?php

namespace App\Controllers;

use App\Services\DistanceService;

class DistanceController {
    public function __construct(
        private DistanceService $distanceService
    ) {
        $this->distanceService = $distanceService;
    }

    public function getDistance(
        string $address,
        string $city,
        string $zipcode
    ): array {
        $distanceKm = $this->distanceService->getDistanceKm(
            $address,
            $city,
            $zipcode
        );

        return [
            'distanceKm' => $distanceKm
        ];
    }
}