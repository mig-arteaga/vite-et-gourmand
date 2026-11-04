<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\CommandeRepository;
use App\Repositories\StatsNoSqlRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\MailService;

class AdminController {
    private UtilisateurRepository $utilisateurRepository;
    private ?CommandeRepository $commandeRepository;
    private ?StatsNoSqlRepository $statsNoSqlRepository;
    private MailService $mailService;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
        ?CommandeRepository $commandeRepository = null,
        ?MailService $mailService = null,
        ?StatsNoSqlRepository $statsNoSqlRepository = null
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->commandeRepository = $commandeRepository;
        $this->mailService = $mailService ?? new MailService();
        $this->statsNoSqlRepository = $statsNoSqlRepository;
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

    /**
     * Retourne les statistiques globales et filtrées (commandes par menu et chiffre d'affaires) depuis NoSQL.
     *
     * @return array{
     *     engine: string,
     *     filters: array{menuId: ?int, startDate: string, endDate: string},
     *     chartStats: array<int, array<string, mixed>>,
     *     filteredStats: array<int, array<string, mixed>>,
     *     summary: array{totalRevenue: float, totalOrders: int, totalPeople: int, averageBasket: float, maxOrders: int, maxRevenue: float}
     * }
     */
    public function getAnalyticsData(
        ?int $menuId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $validMenuId = ($menuId !== null && $menuId > 0) ? $menuId : null;
        $validStartDate = ($startDate !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate))
            ? $startDate
            : '';
        $validEndDate = ($endDate !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate))
            ? $endDate
            : '';

        if (
            $validStartDate !== '' &&
            $validEndDate !== '' &&
            $validStartDate > $validEndDate
        ) {
            [$validStartDate, $validEndDate] = [$validEndDate, $validStartDate];
        }

        if ($this->commandeRepository === null && $this->statsNoSqlRepository === null) {
            return [
                'engine' => 'Indisponible',
                'filters' => [
                    'menuId' => $validMenuId,
                    'startDate' => $validStartDate,
                    'endDate' => $validEndDate
                ],
                'chartStats' => [],
                'filteredStats' => [],
                'summary' => [
                    'totalRevenue' => 0.0,
                    'totalOrders' => 0,
                    'totalPeople' => 0,
                    'averageBasket' => 0.0,
                    'maxOrders' => 1,
                    'maxRevenue' => 1.0
                ]
            ];
        }

        if ($this->statsNoSqlRepository !== null) {
            $chartResult = $this->statsNoSqlRepository->getRevenueStats(
                null,
                $validStartDate !== '' ? $validStartDate : null,
                $validEndDate !== '' ? $validEndDate : null
            );

            $engine = $chartResult['engine'];
            $chartStats = $chartResult['stats'];

            $filteredStats = $validMenuId !== null
                ? $this->statsNoSqlRepository->getRevenueStats(
                    $validMenuId,
                    $validStartDate !== '' ? $validStartDate : null,
                    $validEndDate !== '' ? $validEndDate : null
                )['stats']
                : $chartStats;
        } else {
            $engine = 'MySQL (Relationnel)';
            $chartStats = $this->commandeRepository->getRevenueStats(
                null,
                $validStartDate !== '' ? $validStartDate : null,
                $validEndDate !== '' ? $validEndDate : null
            );

            $filteredStats = $validMenuId !== null
                ? $this->commandeRepository->getRevenueStats(
                    $validMenuId,
                    $validStartDate !== '' ? $validStartDate : null,
                    $validEndDate !== '' ? $validEndDate : null
                )
                : $chartStats;
        }

        $totalRevenue = 0.0;
        $totalOrders = 0;
        $totalPeople = 0;
        $maxOrders = 0;
        $maxRevenue = 0.0;

        foreach ($filteredStats as $row) {
            $totalRevenue += (float) $row['chiffre_affaires'];
            $totalOrders += (int) $row['nb_commandes'];
            $totalPeople += (int) $row['total_personnes'];
        }

        foreach ($chartStats as $row) {
            if ((int) $row['nb_commandes'] > $maxOrders) {
                $maxOrders = (int) $row['nb_commandes'];
            }
            if ((float) $row['chiffre_affaires'] > $maxRevenue) {
                $maxRevenue = (float) $row['chiffre_affaires'];
            }
        }

        $averageBasket = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0.0;

        return [
            'engine' => $engine,
            'filters' => [
                'menuId' => $validMenuId,
                'startDate' => $validStartDate,
                'endDate' => $validEndDate
            ],
            'chartStats' => $chartStats,
            'filteredStats' => $filteredStats,
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'totalOrders' => $totalOrders,
                'totalPeople' => $totalPeople,
                'averageBasket' => $averageBasket,
                'maxOrders' => max(1, $maxOrders),
                'maxRevenue' => max(1.0, $maxRevenue)
            ]
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
