<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Utilisateur;
use App\Repositories\CommandeRepository;
use App\Repositories\MenuRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\CommandeService;
use DateTimeImmutable;

class CompteController {
    private UtilisateurRepository $utilisateurRepository;
    private ?CommandeRepository $commandeRepository;
    private ?MenuRepository $menuRepository;
    private CommandeService $commandeService;

    public function __construct(
        UtilisateurRepository $utilisateurRepository,
        ?CommandeRepository $commandeRepository = null,
        ?MenuRepository $menuRepository = null,
        ?CommandeService $commandeService = null
    ) {
        $this->utilisateurRepository = $utilisateurRepository;
        $this->commandeRepository = $commandeRepository;
        $this->menuRepository = $menuRepository;
        $this->commandeService = $commandeService ?? new CommandeService();
    }

    public function getUser(int $userId): ?Utilisateur {
        return $this->utilisateurRepository->findById($userId);
    }

    public function getUserOrders(int $userId): array {
        if ($this->commandeRepository === null || $userId <= 0) {
            return [];
        }

        return $this->commandeRepository->findByUserId($userId);
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

    /**
     * Permet au client de modifier sa commande tant qu'elle est au statut 'En attente'
     * (toutes les données sont modifiables sauf le menu).
     *
     * @return array{success: bool, message: string}
     */
    public function updateOrderByClient(
        int $userId,
        int $orderId,
        string $deliveryDate,
        string $deliveryTime,
        string $deliveryAddress,
        string $deliveryPostalCode,
        string $deliveryCity,
        int $peopleCount,
        bool $equipmentLoan
    ): array {
        if ($this->commandeRepository === null || $this->menuRepository === null) {
            return [
                'success' => false,
                'message' => 'Service de commande indisponible.'
            ];
        }

        $order = $this->commandeRepository->findById($orderId);

        if ($order === null || $order->getUser()->getId() !== $userId) {
            return [
                'success' => false,
                'message' => 'Commande introuvable.'
            ];
        }

        if ($order->getStatus() !== 'En attente') {
            return [
                'success' => false,
                'message' => 'Seules les commandes ayant le statut « En attente » peuvent être modifiées.'
            ];
        }

        $deliveryDate = trim($deliveryDate);
        $deliveryTime = substr(trim($deliveryTime), 0, 5);
        $deliveryAddress = trim($deliveryAddress);
        $deliveryPostalCode = trim($deliveryPostalCode);
        $deliveryCity = trim($deliveryCity);

        if (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deliveryDate) ||
            !preg_match('/^\d{2}:\d{2}$/', $deliveryTime) ||
            !$this->commandeService->isDeliveryTimeValid($deliveryTime) ||
            $deliveryAddress === '' ||
            mb_strlen($deliveryAddress) > 255 ||
            !preg_match('/^\d{5}$/', $deliveryPostalCode) ||
            $deliveryCity === '' ||
            mb_strlen($deliveryCity) > 100
        ) {
            return [
                'success' => false,
                'message' => 'Veuillez renseigner une date, une heure (entre 08h30 et 22h30) et une adresse de livraison valides.'
            ];
        }

        $menu = $order->getMenu();
        $minPeople = $menu->getMinPeople();
        $previousPeople = $order->getPeopleCount();
        $availableStockForOrder = $menu->getStock() + $previousPeople;

        if ($peopleCount < $minPeople || $peopleCount > $availableStockForOrder) {
            return [
                'success' => false,
                'message' => "Le nombre de personnes pour le menu « {$menu->getTitle()} » doit être compris entre {$minPeople} et {$availableStockForOrder}."
            ];
        }

        $deliveryDateObj = DateTimeImmutable::createFromFormat('Y-m-d', $deliveryDate);
        if (
            $deliveryDateObj === false ||
            !$this->commandeService->isDeliveryDateValid($deliveryDateObj, $menu->getDaysBefore())
        ) {
            return [
                'success' => false,
                'message' => "La date de livraison doit respecter le délai minimum de {$menu->getDaysBefore()} jours avant la prestation."
            ];
        }

        $peopleDelta = $peopleCount - $previousPeople;
        if ($peopleDelta > 0) {
            if (!$this->menuRepository->reserveStock($menu->getId(), $peopleDelta)) {
                return [
                    'success' => false,
                    'message' => 'Stock insuffisant pour augmenter le nombre de convives.'
                ];
            }
        } elseif ($peopleDelta < 0) {
            $this->menuRepository->restoreStock($menu->getId(), abs($peopleDelta));
        }

        // Conserver les frais kilométriques si livraison hors Grenoble ou 0 si Grenoble
        $isGrenoble = mb_strtolower($deliveryCity) === 'grenoble' && $deliveryPostalCode === '38000';
        $distanceKm = $isGrenoble
            ? 0.0
            : ($order->getDistanceFee() > 0 ? round($order->getDistanceFee() / 0.59, 2) : 0.0);

        $prices = $this->commandeService->calculatePrice(
            $menu->getUnitPrice(),
            $peopleCount,
            $minPeople,
            $distanceKm
        );

        $this->commandeRepository->updateByClient(
            $orderId,
            $userId,
            $deliveryDate,
            $deliveryTime . ':00',
            $deliveryAddress,
            $deliveryCity,
            $deliveryPostalCode,
            $peopleCount,
            (float) $prices['menuPrice'],
            (float) $prices['deliveryFee'],
            (float) $prices['groupDiscount'],
            (float) $prices['distanceFee'],
            (float) $prices['totalPrice'],
            $equipmentLoan
        );

        return [
            'success' => true,
            'message' => "Votre commande n°{$orderId} a bien été modifiée."
        ];
    }

    /**
     * Permet au client d'annuler sa commande tant qu'elle est au statut 'En attente'.
     *
     * @return array{success: bool, message: string}
     */
    public function cancelOrderByClient(int $userId, int $orderId): array {
        if ($this->commandeRepository === null || $this->menuRepository === null) {
            return [
                'success' => false,
                'message' => 'Service de commande indisponible.'
            ];
        }

        $order = $this->commandeRepository->findById($orderId);

        if ($order === null || $order->getUser()->getId() !== $userId) {
            return [
                'success' => false,
                'message' => 'Commande introuvable.'
            ];
        }

        if ($order->getStatus() !== 'En attente') {
            return [
                'success' => false,
                'message' => 'Seules les commandes ayant le statut « En attente » peuvent être annulées depuis votre espace client.'
            ];
        }

        if ($this->commandeRepository->cancelByClient($orderId, $userId)) {
            $this->commandeRepository->addStatusHistory($orderId, 'Annulée');
            $this->menuRepository->restoreStock(
                $order->getMenu()->getId(),
                $order->getPeopleCount()
            );

            return [
                'success' => true,
                'message' => "Votre commande n°{$orderId} a bien été annulée."
            ];
        }

        return [
            'success' => false,
            'message' => 'Impossible d\'annuler cette commande.'
        ];
    }
}

