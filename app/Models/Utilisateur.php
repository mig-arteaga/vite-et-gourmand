<?php

declare(strict_types=1);

namespace App\Models;

class Utilisateur {
    private int $id;
    private string $lastName;
    private string $firstName;
    private string $email;
    private string $password;
    private string $phone;
    private string $address;
    private int $postalCode;
    private string $city;
    private ?string $photo;
    private Role $role;

    public function __construct(
        int $id,
        string $lastName,
        string $firstName,
        string $email,
        string $password,
        string $phone,
        string $address,
        int $postalCode,
        string $city,
        ?string $photo,
        Role $role
    ) {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->address = $address;
        $this->postalCode = $postalCode;
        $this->city = $city;
        $this->photo = $photo;
        $this->role = $role;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function getPhone(): string {
        return $this->phone;
    }

    public function getAddress(): string {
        return $this->address;
    }

    public function getPostalCode(): int {
        return $this->postalCode;
    }

    public function getCity(): string {
        return $this->city;
    }

    public function getPhoto(): ?string {
        return $this->photo;
    }

    public function getRole(): Role {
        return $this->role;
    }
}