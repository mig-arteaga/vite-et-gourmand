<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class Commande {
    private int $id;
    private Utilisateur $user;
    private Menu $menu;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $deliveryDate;
    private int $peopleCount;
    private float $menuPrice;
    private float $deliveryPrice;
    private bool $equipmentLoan;
    private bool $returned;
    private string $status;

    public function __construct(
        int $id,
        Utilisateur $user,
        Menu $menu,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $deliveryDate,
        int $peopleCount,
        float $menuPrice,
        float $deliveryPrice,
        bool $equipmentLoan,
        bool $returned,
        string $status
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->menu = $menu;
        $this->createdAt = $createdAt;
        $this->deliveryDate = $deliveryDate;
        $this->peopleCount = $peopleCount;
        $this->menuPrice = $menuPrice;
        $this->deliveryPrice = $deliveryPrice;
        $this->equipmentLoan = $equipmentLoan;
        $this->returned = $returned;
        $this->status = $status;
    }

    public function getId(): int {
        return $this->id;
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
}