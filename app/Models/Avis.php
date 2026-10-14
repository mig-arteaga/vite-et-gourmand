<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;

class Avis {
    private int $id;
    private Utilisateur $user;
    private DateTimeImmutable $date;
    private int $rating;
    private string $message;
    private string $status;

    public function __construct(
        int $id,
        Utilisateur $user,
        DateTimeImmutable $date,
        int $rating,
        string $message,
        string $status
    ) {
        $this->id = $id;
        $this->user = $user;
        $this->date = $date;
        $this->rating = $rating;
        $this->message = $message;
        $this->status = $status;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getUser(): Utilisateur {
        return $this->user;
    }

    public function getDate(): DateTimeImmutable {
        return $this->date;
    }

    public function getRating(): int {
        return $this->rating;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function getStatus(): string {
        return $this->status;
    }
}