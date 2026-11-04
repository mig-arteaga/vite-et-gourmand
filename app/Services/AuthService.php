<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Utilisateur;
use App\Repositories\UtilisateurRepository;

class AuthService {
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(UtilisateurRepository $utilisateurRepository) {
        $this->utilisateurRepository = $utilisateurRepository;
    }

    public function authenticate(string $email, string $password): ?Utilisateur {
        $user = $this->utilisateurRepository->findByEmail(strtolower(trim($email)));

        if ($user === null || !$user->isActive()) {
            return null;
        }

        if (!password_verify($password, $user->getPassword())) {
            return null;
        }

        return $user;
    }

    /**
     * Vérifie que le mot de passe respecte les exigences de sécurité ECF :
     * 10 caractères minimum, 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial.
     */
    public function isValidStrongPassword(string $password): bool {
        if (mb_strlen($password) < 10) {
            return false;
        }

        if (!preg_match('/[A-Z]/', $password)) {
            return false;
        }

        if (!preg_match('/[a-z]/', $password)) {
            return false;
        }

        if (!preg_match('/\d/', $password)) {
            return false;
        }

        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            return false;
        }

        return true;
    }
}
