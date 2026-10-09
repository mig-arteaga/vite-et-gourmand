<?php

namespace App\Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }

    public static function getRole(): ?string {
        return $_SESSION['role'] ?? null;
    }

    public static function isAdmin(): bool {
        return self::getRole() === 'Administrateur';
    }

    public static function isEmployee(): bool {
        return in_array(
            self::getRole(),
            ['Administrateur', 'Employé'],
            true
        );
    }

    public static function hasRole(array $roles): bool {
        return in_array(self::getRole(), $roles, true);
    }

    public static function requireRoles(array $roles): void {
        self::start();

        if (!self::isLoggedIn()) {
            header('Location: /login');
            exit;
        }

        if (!self::hasRole($roles)) {
            header('Location: /');
            exit;
        }
    }
}