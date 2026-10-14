<?php

declare(strict_types=1);

namespace App\Models;

class Plat {
    private int $id;
    private string $title;
    private string $description;
    private string $type;

    /** @var Allergene[] */
    private array $allergens;

    public function __construct(
        int $id,
        string $title,
        string $description,
        string $type,
        array $allergens = []
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->type = $type;
        $this->allergens = $allergens;
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

    public function getType(): string {
        return $this->type;
    }

    /**
     * @return Allergene[]
     */
    public function getAllergens(): array {
        return $this->allergens;
    }
}