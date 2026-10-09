<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\ThemeRepository;
use App\Models\Menu;

class MenuController {
    private MenuRepository $menuRepository;
    private ThemeRepository $themeRepository;
    private RegimeRepository $regimeRepository;

    public function __construct(
        MenuRepository $menuRepository,
        ThemeRepository $themeRepository,
        RegimeRepository $regimeRepository
    ) {
        $this->menuRepository = $menuRepository;
        $this->themeRepository = $themeRepository;
        $this->regimeRepository = $regimeRepository;
    }

    public function getAll(): array {
        return $this->menuRepository->findAll();
    }

    public function getFiltered(array $filters): array {
        return $this->menuRepository->findByFilters($filters);
    }

    public function getDetail(int $menuId): ?Menu {
        return $this->menuRepository->findById($menuId);
    }

    public function getDetailData(int $menuId): ?array {
        $menu = $this->getDetail($menuId);

        if ($menu === null) {
            return null;
        }

        $photos = [];

        foreach ($menu->getPhotos() as $photo) {
            $photos[] = [
                'path' => $photo->getPath()
            ];
        }

        $dishes = [];
        $allergens = [];

        foreach ($menu->getDishes() as $dish) {
            $dishes[] = [
                'title' => $dish->getTitle(),
                'type' => $dish->getType()
            ];

            foreach ($dish->getAllergens() as $allergen) {
                $allergens[$allergen->getId()] = [
                    'allergenic' => $allergen->getLabel()
                ];
            }
        }

        return [
            'menu' => [
                'title' => $menu->getTitle(),
                'description' => $menu->getDescription(),
                'theme' => $menu->getTheme()->getLabel(),
                'diet' => $menu->getRegime()->getLabel(),
                'min_people' => $menu->getMinPeople(),
                'unit_price' => $menu->getUnitPrice(),
                'delay' => $menu->getDaysBefore(),
                'stock' => $menu->getStock()
            ],
            'photos' => $photos,
            'dishes' => $dishes,
            'allergenics' => array_values($allergens)
        ];
    }

    public function getFilterValues(): array {
        return [
            'prices' => $this->menuRepository->getPriceRange(),
            'themes' => array_map(
                fn($theme) => ['theme' => $theme->getLabel()],
                $this->themeRepository->findAll()
            ),
            'diets' => array_map(
                fn($diet) => ['diet' => $diet->getLabel()],
                $this->regimeRepository->findAll()
            ),
            'people' => $this->menuRepository->getPeopleRange()
        ];
    }
}