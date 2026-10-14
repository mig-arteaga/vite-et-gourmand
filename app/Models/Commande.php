<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class Commande {
    private int $id;
    private string $orderToken;
    private Utilisateur $user;
    private Menu $menu;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $deliveryDate;
    private string $deliveryTime;
    private string $deliveryAddress;
    private string $deliveryCity;
    private string $deliveryPostalCode;
    private int $peopleCount;
    private float $menuPrice;
    private float $deliveryPrice;
    private float $groupDiscount;
    private float $distanceFee;
    private float $totalPrice;
    private bool $equipmentLoan;
    private bool $returned;
    private string $status;

    public function __construct(
        int $id,
        string $orderToken,
        Utilisateur $user,
        Menu $menu,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $deliveryDate,
        string $deliveryTime,
        string $deliveryAddress,
        string $deliveryCity,
        string $deliveryPostalCode,
        int $peopleCount,
        float $menuPrice,
        float $deliveryPrice,
        float $groupDiscount,
        float $distanceFee,
        float $totalPrice,
        bool $equipmentLoan,
        bool $returned,
        string $status
    ) {
        $this->id = $id;
        $this->orderToken = $orderToken;
        $this->user = $user;
        $this->menu = $menu;
        $this->createdAt = $createdAt;
        $this->deliveryDate = $deliveryDate;
        $this->deliveryTime = $deliveryTime;
        $this->deliveryAddress = $deliveryAddress;
        $this->deliveryCity = $deliveryCity;
        $this->deliveryPostalCode = $deliveryPostalCode;
        $this->peopleCount = $peopleCount;
        $this->menuPrice = $menuPrice;
        $this->deliveryPrice = $deliveryPrice;
        $this->groupDiscount = $groupDiscount;
        $this->distanceFee = $distanceFee;
        $this->totalPrice = $totalPrice;
        $this->equipmentLoan = $equipmentLoan;
        $this->returned = $returned;
        $this->status = $status;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getOrderToken(): string {
        return $this->orderToken;
    }

    public function getUser(): Utilisateur {
        return $this->user;
    }

    public function getMenu(): Menu {
        return $this->menu;
    }

    public function getCreatedAt(): DateTimeImmutable {
        return $this->createdAt;
    }

    public function getDeliveryDate(): DateTimeImmutable {
        return $this->deliveryDate;
    }

    public function getPeopleCount(): int {
        return $this->peopleCount;
    }

    public function getMenuPrice(): float {
        return $this->menuPrice;
    }

    public function getDeliveryPrice(): float {
        return $this->deliveryPrice;
    }

    public function hasEquipmentLoan(): bool {
        return $this->equipmentLoan;
    }

    public function isReturned(): bool {
        return $this->returned;
    }

    public function getStatus(): string {
        return $this->status;
    }

    public function getDeliveryTime(): string {
        return $this->deliveryTime;
    }

    public function getDeliveryAddress(): string {
        return $this->deliveryAddress;
    }

    public function getDeliveryCity(): string {
        return $this->deliveryCity;
    }

    public function getDeliveryPostalCode(): string {
        return $this->deliveryPostalCode;
    }

    public function getGroupDiscount(): float {
        return $this->groupDiscount;
    }

    public function getDistanceFee(): float {
        return $this->distanceFee;
    }

    public function getTotalPrice(): float {
        return $this->totalPrice;
    }
}