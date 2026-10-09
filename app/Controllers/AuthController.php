<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\AuthService;

class AuthController {
    private AuthService $authService;

    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(string $email, string $password): ?string {
        $user = $this->authService->authenticate($email, $password);

        if ($user === null) {
            return null;
        }

        $_SESSION['user_id'] = $user->getId();
        $_SESSION['firstname'] = $user->getFirstName();
        $_SESSION['role'] = $user->getRole()->getLabel();
        $_SESSION['photo'] = $user->getPhoto();

        return match ($user->getRole()->getLabel()) {
            'Administrateur' => '/admin',
            'Employé' => '/employe',
            default => '/'
        };
    }

    public function logout(): string {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        return '/';
    }
}