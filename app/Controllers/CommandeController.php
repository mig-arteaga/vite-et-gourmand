<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\UtilisateurRepository;
use App\Services\CommandeService;
use App\Models\Menu;
use DateTimeImmutable;

class CommandeController {
    private MenuRepository $menuRepository;
    private UtilisateurRepository $utilisateurRepository;
    private CommandeService $commandeService;

    public function __construct(
        MenuRepository $menuRepository,
        UtilisateurRepository $utilisateurRepository,
        CommandeService $commandeService
    ) {
        $this->menuRepository = $menuRepository;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->commandeService = $commandeService;
    }

    public function getOrderData(int $menuId, int $userId): array {
        return [
            'menu' => $this->menuRepository->findById($menuId),
            'user' => $this->utilisateurRepository->findById($userId),
            'menuList' => $this->menuRepository->findAll()
        ];
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