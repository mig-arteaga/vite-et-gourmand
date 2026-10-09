<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;

class CommandeService {
    private const DELIVERY_FEE = 5.0;
    private const GROUP_DISCOUNT_PERCENTAGE = 0.10;
    private const DISTANCE_FEE_PER_KM = 0.59;

    public function calculatePrice(
        float $unitPrice,
        int $people,
        int $minPeople,
        float $distanceKm
    ): array {
        $menuPrice = $unitPrice * $people;

        $groupDiscount = 0.0;

        if ($people >= ($minPeople + 5)) {
            $groupDiscount = $menuPrice * self::GROUP_DISCOUNT_PERCENTAGE;
        }

        $distanceFee = 0.0;

        if ($distanceKm > 0) {
            $roundedDistanceKm = ceil($distanceKm);
            $distanceFee = $roundedDistanceKm * self::DISTANCE_FEE_PER_KM;
        }

        $totalPrice =
            $menuPrice
            - $groupDiscount
            + self::DELIVERY_FEE
            + $distanceFee;

        return [
            'menuPrice' => $menuPrice,
            'groupDiscount' => $groupDiscount,
            'deliveryFee' => self::DELIVERY_FEE,
            'distanceFee' => $distanceFee,
            'totalPrice' => $totalPrice
        ];
    }

    public function isPeopleCountValid(
        int $people,
        int $minPeople,
        int $stock
    ): bool {
        return $people >= $minPeople && $people <= $stock;
    }

    public function isDeliveryDateValid(
        DateTimeImmutable $deliveryDate,
        int $daysBefore,
        ?DateTimeImmutable $today = null
    ): bool {
        $today ??= new DateTimeImmutable('today');

        $minDate = $today->modify("+{$daysBefore} days");
        $maxDate = $today->modify('+6 months');

        return $deliveryDate >= $minDate && $deliveryDate <= $maxDate;
    }

    public function isDeliveryTimeValid(string $time): bool {
        $minTime = '08:30';
        $maxTime = '22:30';

        return $time >= $minTime && $time <= $maxTime;
    }
}