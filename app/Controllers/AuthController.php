<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UtilisateurRepository;
use App\Services\AuthService;
use App\Services\MailService;

class AuthController {
    private AuthService $authService;
    private ?UtilisateurRepository $utilisateurRepository;
    private MailService $mailService;

    public function __construct(
        AuthService $authService,
        ?UtilisateurRepository $utilisateurRepository = null,
        ?MailService $mailService = null
    ) {
        $this->authService = $authService;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->mailService = $mailService ?? new MailService();
    }

    public function login(string $email, string $password): ?string {
        $user = $this->authService->authenticate($email, $password);

        if ($user === null) {
            return null;
        }

        session_regenerate_id(true);

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
            $params = session_get_cookie_params();

            setcookie(session_name(), '', [
                'expires' => time() - 3600,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => $params['secure'],
                'httponly' => $params['httponly'],
                'samesite' => $params['samesite']
            ]);

            session_destroy();
        }

        return '/';
    }

    /**
     * Crée un nouveau compte client (rôle Utilisateur) et envoie l'e-mail de bienvenue.
     *
     * @return array{success: bool, message: string}
     */
    public function register(
        string $lastName,
        string $firstName,
        string $email,
        string $phone,
        string $address,
        string $postalCode,
        string $city,
        string $password,
        string $passwordConfirm
    ): array {
        if ($this->utilisateurRepository === null) {
            return [
                'success' => false,
                'message' => 'Service d\'inscription indisponible.'
            ];
        }

        $lastName = trim($lastName);
        $firstName = trim($firstName);
        $email = strtolower(trim($email));
        $phone = trim($phone);
        $address = trim($address);
        $postalCode = trim($postalCode);
        $city = trim($city);

        if (
            $lastName === '' || mb_strlen($lastName) > 50 ||
            $firstName === '' || mb_strlen($firstName) > 50 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100 ||
            $phone === '' || mb_strlen($phone) > 50 ||
            $address === '' || mb_strlen($address) > 100 ||
            !preg_match('/^\d{5}$/', $postalCode) ||
            $city === '' || mb_strlen($city) > 50
        ) {
            return [
                'success' => false,
                'message' => 'Veuillez renseigner tous les champs obligatoires avec des informations valides (code postal à 5 chiffres).'
            ];
        }

        if ($password !== $passwordConfirm) {
            return [
                'success' => false,
                'message' => 'Les deux mots de passe saisis ne correspondent pas.'
            ];
        }

        if (!$this->authService->isValidStrongPassword($password)) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au minimum 10 caractères, dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.'
            ];
        }

        if ($this->utilisateurRepository->findByEmail($email) !== null) {
            return [
                'success' => false,
                'message' => 'Un compte est déjà associé à cette adresse e-mail.'
            ];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->utilisateurRepository->createClient(
            $lastName,
            $firstName,
            $email,
            $passwordHash,
            $phone,
            $address,
            (int) $postalCode,
            $city
        );

        $this->mailService->sendWelcomeEmail($email, $firstName, $lastName);

        return [
            'success' => true,
            'message' => 'Votre compte a bien été créé ! Un e-mail de bienvenue vous a été envoyé. Vous pouvez dès à présent vous connecter.'
        ];
    }

    /**
     * Génère un lien de réinitialisation de mot de passe (valable 1h) et l'envoie par e-mail.
     *
     * @return array{success: bool, message: string, demo_reset_url?: string}
     */
    public function requestPasswordReset(string $email, string $baseUrl): array {
        if ($this->utilisateurRepository === null) {
            return [
                'success' => false,
                'message' => 'Service de réinitialisation indisponible.'
            ];
        }

        $email = strtolower(trim($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'Veuillez saisir une adresse e-mail valide.'
            ];
        }

        $user = $this->utilisateurRepository->findByEmail($email);

        $genericMessage = 'Si un compte actif est associé à cette adresse e-mail, un lien de réinitialisation valable 1 heure vient de vous être envoyé.';

        if ($user === null || !$user->isActive()) {
            return [
                'success' => true,
                'message' => $genericMessage
            ];
        }

        $plainToken = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $plainToken);
        $expiresAt = (new \DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');

        $this->utilisateurRepository->saveResetToken(
            $user->getId(),
            $tokenHash,
            $expiresAt
        );

        $resetUrl = rtrim($baseUrl, '/') . '/reset-password?token=' . urlencode($plainToken);

        $this->mailService->sendPasswordResetEmail(
            $user->getEmail(),
            $user->getFirstName(),
            $resetUrl
        );

        return [
            'success' => true,
            'message' => $genericMessage,
            'demo_reset_url' => $resetUrl
        ];
    }

    public function isResetTokenValid(string $plainToken): bool {
        if ($this->utilisateurRepository === null) {
            return false;
        }

        $plainToken = trim($plainToken);
        if (!preg_match('/^[a-f0-9]{64}$/', $plainToken)) {
            return false;
        }

        $tokenHash = hash('sha256', $plainToken);

        return $this->utilisateurRepository->findByValidResetToken($tokenHash) !== null;
    }

    /**
     * Valide le jeton et met à jour le mot de passe de l'utilisateur.
     *
     * @return array{success: bool, message: string}
     */
    public function resetPassword(
        string $plainToken,
        string $password,
        string $passwordConfirm
    ): array {
        if ($this->utilisateurRepository === null) {
            return [
                'success' => false,
                'message' => 'Service de réinitialisation indisponible.'
            ];
        }

        $plainToken = trim($plainToken);
        if (!preg_match('/^[a-f0-9]{64}$/', $plainToken)) {
            return [
                'success' => false,
                'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'
            ];
        }

        $tokenHash = hash('sha256', $plainToken);
        $user = $this->utilisateurRepository->findByValidResetToken($tokenHash);

        if ($user === null) {
            return [
                'success' => false,
                'message' => 'Ce lien de réinitialisation est invalide ou a expiré.'
            ];
        }

        if ($password !== $passwordConfirm) {
            return [
                'success' => false,
                'message' => 'Les deux mots de passe saisis ne correspondent pas.'
            ];
        }

        if (!$this->authService->isValidStrongPassword($password)) {
            return [
                'success' => false,
                'message' => 'Le nouveau mot de passe doit contenir au minimum 10 caractères, dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.'
            ];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->utilisateurRepository->updatePasswordAndClearResetToken(
            $user->getId(),
            $passwordHash
        );

        return [
            'success' => true,
            'message' => 'Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.'
        ];
    }
}
