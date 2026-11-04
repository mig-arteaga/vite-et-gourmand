<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UtilisateurRepository;
use App\Services\MailService;

class AdminController {
    private UtilisateurRepository $utilisateurRepository;
    private MailService $mailService;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
        ?MailService $mailService = null
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->mailService = $mailService ?? new MailService();
    }

    public function getEmployees(): array {
        return $this->utilisateurRepository->findEmployees();
    }

    /**
     * Crée un compte employé (jamais administrateur) et envoie un mail de notification sans le mot de passe.
     *
     * @return array{success: bool, message: string}
     */
    public function createEmployee(
        string $lastName,
        string $firstName,
        string $email,
        string $password,
        string $phone
    ): array {
        $lastName = trim($lastName);
        $firstName = trim($firstName);
        $email = strtolower(trim($email));
        $phone = trim($phone);

        if (
            $lastName === '' ||
            mb_strlen($lastName) > 50 ||
            $firstName === '' ||
            mb_strlen($firstName) > 50 ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            mb_strlen($email) > 100
        ) {
            return [
                'success' => false,
                'message' => 'Veuillez renseigner un nom, un prénom et une adresse e-mail valides.'
            ];
        }

        if ($phone === '' || mb_strlen($phone) > 50) {
            $phone = '+33 4 00 00 00 00';
        }

        if (!$this->isStrongPassword($password)) {
            return [
                'success' => false,
                'message' => 'Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.'
            ];
        }

        if ($this->utilisateurRepository->findByEmail($email) !== null) {
            return [
                'success' => false,
                'message' => 'Un compte existe déjà avec cette adresse e-mail.'
            ];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $this->utilisateurRepository->createEmployee(
            $lastName,
            $firstName,
            $email,
            $passwordHash,
            $phone
        );

        $this->mailService->sendEmployeeAccountCreatedNotice(
            $email,
            $firstName,
            $lastName
        );

        return [
            'success' => true,
            'message' => "Le compte employé de {$firstName} {$lastName} ({$email}) a été créé et un e-mail de notification lui a été envoyé."
        ];
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function toggleEmployeeStatus(int $userId, bool $active): array {
        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => 'Identifiant employé invalide.'
            ];
        }

        $user = $this->utilisateurRepository->findById($userId);

        if ($user === null || $user->getRole()->getLabel() !== 'Employé') {
            return [
                'success' => false,
                'message' => 'Seuls les comptes employés peuvent être activés ou désactivés.'
            ];
        }

        $this->utilisateurRepository->updateEmployeeActiveStatus($userId, $active);

        $statusLabel = $active ? 'réactivé' : 'désactivé';

        return [
            'success' => true,
            'message' => "Le compte employé de {$user->getFirstName()} {$user->getLastName()} a été {$statusLabel}."
        ];
    }

    private function isStrongPassword(string $password): bool {
        if (strlen($password) < 10) {
            return false;
        }

        return (bool) (
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/\d/', $password) &&
            preg_match('/[^A-Za-z0-9]/', $password)
        );
    }
}

