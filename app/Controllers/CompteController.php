<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepository;

class CompteController {
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository) {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function getUser(int $userId): ?Utilisateur {
        return $this->utilisateurRepository->findById($userId);
    }

    /**
     * Met à jour les informations personnelles de l'utilisateur connecté.
     *
     * @return array{success: bool, message: string}
     */
    public function updateProfile(
        int $userId,
        string $lastName,
        string $firstName,
        string $email,
        string $phone,
        string $address,
        string $postalCode,
        string $city
    ): array {
        $lastName = trim($lastName);
        $firstName = trim($firstName);
        $email = strtolower(trim($email));
        $phone = trim($phone);
        $address = trim($address);
        $postalCode = trim($postalCode);
        $city = trim($city);

        if (
            $lastName === '' ||
            mb_strlen($lastName) > 50 ||
            $firstName === '' ||
            mb_strlen($firstName) > 50 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            mb_strlen($email) > 100 ||
            $phone === '' ||
            mb_strlen($phone) > 50 ||
            $address === '' ||
            mb_strlen($address) > 100 ||
            !preg_match('/^\d{5}$/', $postalCode) ||
            $city === '' ||
            mb_strlen($city) > 50
        ) {
            return [
                'success' => false,
                'message' => 'Veuillez renseigner tous les champs obligatoires avec des informations valides (code postal à 5 chiffres).'
            ];
        }

        $existingUser = $this->utilisateurRepository->findByEmail($email);

        if ($existingUser !== null && $existingUser->getId() !== $userId) {
            return [
                'success' => false,
                'message' => 'Cette adresse e-mail est déjà utilisée par un autre compte.'
            ];
        }

        $this->utilisateurRepository->updateProfile(
            $userId,
            $lastName,
            $firstName,
            $email,
            $phone,
            $address,
            (int) $postalCode,
            $city
        );

        return [
            'success' => true,
            'message' => 'Vos informations personnelles ont bien été mises à jour.'
        ];
    }
}

