<?php

declare(strict_types=1);

namespace App\Models;

class Menu {
    private int $id;
    private string $title;
    private string $description;
    private Theme $theme;
    private Regime $regime;
    private int $minPeople;
    private float $unitPrice;
    private int $daysBefore;
    private int $stock;

    /** @var Plat[] */
    private array $dishes;

    /** @var PhotoMenu[] */
    private array $photos;

    public function __construct(
        int $id,
        string $title,
        string $description,
        Theme $theme,
        Regime $regime,
        int $minPeople,
        float $unitPrice,
        int $daysBefore,
        int $stock,
        array $dishes = [],
        array $photos = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->theme = $theme;
        $this->regime = $regime;
        $this->minPeople = $minPeople;
        $this->unitPrice = $unitPrice;
        $this->daysBefore = $daysBefore;
        $this->stock = $stock;
        $this->dishes = $dishes;
        $this->photos = $photos;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): string {
        return $this->description;
    }

    public function getTheme(): Theme {
        return $this->theme;
    }

    public function getRegime(): Regime {
        return $this->regime;
    }

    public function getMinPeople(): int {
        return $this->minPeople;
    }

    public function getUnitPrice(): float {
        return $this->unitPrice;
    }

    public function getDaysBefore(): int {
        return $this->daysBefore;
    }

    public function getStock(): int {
        return $this->stock;
    }

    /**
     * @return Plat[]
     */
    public function getDishes(): array {
        return $this->dishes;
    }

    /**
     * @return PhotoMenu[]
     */
    public function getPhotos(): array {
        return $this->photos;
    }
}