<?php

declare(strict_types=1);

namespace App\Models;

class PhotoMenu {
    private int $id;
    private string $path;
    private int $order;

    public function __construct(
        int $id,
        string $path,
        int $order
    ) {
        $this->id = $id;
        $this->path = $path;
        $this->order = $order;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getPath(): string {
        return $this->path;
    }

    public function getOrder(): int {
        return $this->order;
    }
}