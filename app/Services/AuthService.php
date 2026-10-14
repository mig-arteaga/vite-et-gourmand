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
        $user = $this->utilisateurRepository->findByEmail($email);

        if ($user === null) {
            return null;
        }

        if (!password_verify($password, $user->getPassword())) {
            return null;
        }

        return $user;
    }
}