<?php

declare(strict_types=1);

namespace App\Models;

class Horaire {
    private int $id;
    private string $day;
    private ?string $openingTime;
    private ?string $closingTime;
    private bool $closed;

    public function __construct(
        int $id,
        string $day,
        ?string $openingTime,
        ?string $closingTime,
        bool $closed
    ) {
        $this->id = $id;
        $this->day = $day;
        $this->openingTime = $openingTime;
        $this->closingTime = $closingTime;
        $this->closed = $closed;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getDay(): string {
        return $this->day;
    }

    public function getOpeningTime(): ?string {
        return $this->openingTime;
    }

    public function getClosingTime(): ?string {
        return $this->closingTime;
    }

    public function isClosed(): bool {
        return $this->closed;
    }

    public function getOpeningTimeForInput(): string {
        if ($this->openingTime === null || $this->openingTime === '') {
            return '11:30';
        }

        return substr($this->openingTime, 0, 5);
    }

    public function getClosingTimeForInput(): string {
        if ($this->closingTime === null || $this->closingTime === '') {
            return '22:30';
        }

        return substr($this->closingTime, 0, 5);
    }

    public function getFormattedSlot(): string {
        if ($this->closed || $this->openingTime === null || $this->closingTime === null) {
            return 'Fermé';
        }

        $open = str_replace(':', 'h', substr($this->openingTime, 0, 5));
        $close = str_replace(':', 'h', substr($this->closingTime, 0, 5));

        return "{$open} – {$close}";
    }
}

