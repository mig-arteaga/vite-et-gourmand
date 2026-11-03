<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\UtilisateurRepository;
use App\Repositories\CommandeRepository;
use App\Services\CommandeService;
use App\Services\MailService;
use App\Models\Menu;
use DateTimeImmutable;

class CommandeController {
    private MenuRepository $menuRepository;
    private UtilisateurRepository $utilisateurRepository;
    private CommandeRepository $commandeRepository;
    private CommandeService $commandeService;

    private const WORKFLOW_STATUSES = [
        'En attente',
        'Accepté',
        'En préparation',
        'En cours de livraison',
        'Livré',
        'En attente du retour de matériel',
        'Terminée'
    ];

    public function __construct(
        MenuRepository $menuRepository,
        UtilisateurRepository $utilisateurRepository,
        CommandeRepository $commandeRepository,
        CommandeService $commandeService
    ) {
        $this->menuRepository = $menuRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->commandeRepository = $commandeRepository;
        $this->commandeService = $commandeService;
    }

    public function getOrderData(int $menuId, int $userId): array {
        return [
            'menu' => $this->menuRepository->findById($menuId),
            'user' => $this->utilisateurRepository->findById($userId),
            'menuList' => $this->menuRepository->findAll()
        ];
    }

    public function getAllOrders(): array {
        return $this->commandeRepository->findAll();
    }

    public function getAllowedStatuses(): array {
        return self::WORKFLOW_STATUSES;
    }

    public function updateOrderStatusByEmployee(
        int $orderId,
        string $newStatus,
        bool $returned
    ): bool {
        if ($orderId <= 0 || !in_array($newStatus, self::WORKFLOW_STATUSES, true)) {
            return false;
        }

        $order = $this->commandeRepository->findById($orderId);

        if ($order === null || $order->getStatus() === 'Annulée') {
            return false;
        }

        // Le statut "En attente du retour de matériel" ne concerne que les commandes avec prêt de matériel
        if ($newStatus === 'En attente du retour de matériel' && !$order->hasEquipmentLoan()) {
            return false;
        }

        // Respecter la contrainte SQL chk_restitution_coherente
        if (!$order->hasEquipmentLoan()) {
            $returned = false;
        } elseif ($newStatus === 'Terminée') {
            // Selon l'énoncé : "terminée : soit livrée sans prêt de matériel, soit quand le matériel a été restitué"
            $returned = true;
        }

        $previousStatus = $order->getStatus();

        $updated = $this->commandeRepository->updateStatus(
            $orderId,
            $newStatus,
            $returned
        );

        if (!$updated) {
            return false;
        }

        if ($previousStatus !== $newStatus) {
            $this->commandeRepository->addStatusHistory($orderId, $newStatus);

            $mailService = new MailService();

            if ($newStatus === 'En attente du retour de matériel') {
                $mailService->sendEquipmentReturnNotice(
                    $order->getUser()->getEmail(),
                    $order->getUser()->getFirstName(),
                    $orderId
                );
            } elseif ($newStatus === 'Terminée') {
                $mailService->sendOrderCompletedReviewNotice(
                    $order->getUser()->getEmail(),
                    $order->getUser()->getFirstName(),
                    $orderId
                );
            }
        }

        return true;
    }

    public function cancelOrderByEmployee(
        int $orderId,
        string $contactMode,
        string $reason
    ): bool {
        $allowedContactModes = ['Appel GSM', 'Mail'];
        $trimmedReason = trim($reason);

        if (
            $orderId <= 0 ||
            !in_array($contactMode, $allowedContactModes, true) ||
            $trimmedReason === ''
        ) {
            return false;
        }

        $order = $this->commandeRepository->findById($orderId);

        if (
            $order === null ||
            $order->getStatus() === 'Terminée' ||
            $order->getStatus() === 'Annulée'
        ) {
            return false;
        }

        $cancelled = $this->commandeRepository->cancelByEmployee(
            $orderId,
            $contactMode,
            $trimmedReason
        );

        if (!$cancelled) {
            return false;
        }

        $this->commandeRepository->addStatusHistory($orderId, 'Annulée');

        $this->menuRepository->restoreStock(
            $order->getMenu()->getId(),
            $order->getPeopleCount()
        );

        return true;
    }

    public function changeMenu(int $menuId): ?Menu {
        return $this->menuRepository->findById($menuId);
    }

    public function getChangeMenuData(int $menuId): ?array {
        $menu = $this->changeMenu($menuId);

        if ($menu === null) {
            return null;
        }

        $photo = '';

        foreach ($menu->getPhotos() as $menuPhoto) {
            if ($menuPhoto->getOrder() === 1) {
                $photo = $menuPhoto->getPath();
                break;
            }
        }

        $dishes = [];

        foreach ($menu->getDishes() as $dish) {
            $dishes[] = [
                'title' => $dish->getTitle(),
                'type' => $dish->getType()
            ];
        }

        return [
            'menu' => [
                'photo' => $photo,
                'title' => $menu->getTitle(),
                'min_people' => $menu->getMinPeople(),
                'unit_price' => $menu->getUnitPrice(),
                'delay' => $menu->getDaysBefore(),
                'stock' => $menu->getStock()
            ],
            'dishes' => $dishes
        ];
    }

    public function calculatePrice(
        int $menuId,
        int $people,
        float $distanceKm
    ): array {
        if (!is_finite($distanceKm) || $distanceKm < 0) {
            return [
                'status' => 422,
                'error' => 'Distance invalide'
            ];
        }

        $menu = $this->menuRepository->findById($menuId);

        if ($menu === null) {
            return [
                'status' => 404,
                'error' => 'Menu introuvable'
            ];
        }

        if (!$this->commandeService->isPeopleCountValid(
            $people,
            $menu->getMinPeople(),
            $menu->getStock()
        )) {
            return [
                'status' => 422,
                'error' => 'Nombre de personnes invalide'
            ];
        }

        return [
            'status' => 200,
            'prices' => $this->commandeService->calculatePrice(
                $menu->getUnitPrice(),
                $people,
                $menu->getMinPeople(),
                $distanceKm
            )
        ];
    }

    public function isPeopleCountValid(int $menuId, int $people): bool {
        $menu = $this->menuRepository->findById($menuId);

        if ($menu === null) {
            return false;
        }

        return $this->commandeService->isPeopleCountValid(
            $people,
            $menu->getMinPeople(),
            $menu->getStock()
        );
    }

    public function isDeliveryDateValid(
        int $menuId,
        DateTimeImmutable $deliveryDate,
        ?DateTimeImmutable $today = null
    ): bool {
        $menu = $this->menuRepository->findById($menuId);

        if ($menu === null) {
            return false;
        }

        return $this->commandeService->isDeliveryDateValid(
            $deliveryDate,
            $menu->getDaysBefore(),
            $today
        );
    }

    public function isDeliveryTimeValid(string $time): bool {
        return $this->commandeService->isDeliveryTimeValid($time);
    }
}
