<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MenuRepository;
use App\Repositories\RegimeRepository;
use App\Repositories\ThemeRepository;
use App\Repositories\PlatRepository;
use App\Repositories\AllergeneRepository;
use App\Repositories\PhotoMenuRepository;
use App\Models\Menu;

class MenuController {
    private MenuRepository $menuRepository;
    private ThemeRepository $themeRepository;
    private RegimeRepository $regimeRepository;
    private ?PlatRepository $platRepository;
    private ?AllergeneRepository $allergeneRepository;
    private ?PhotoMenuRepository $photoMenuRepository;

    public function __construct(
        MenuRepository $menuRepository,
        ThemeRepository $themeRepository,
        RegimeRepository $regimeRepository,
        ?PlatRepository $platRepository = null,
        ?AllergeneRepository $allergeneRepository = null,
        ?PhotoMenuRepository $photoMenuRepository = null
    ) {
        $this->menuRepository = $menuRepository;
        $this->themeRepository = $themeRepository;
        $this->regimeRepository = $regimeRepository;
        $this->platRepository = $platRepository;
        $this->allergeneRepository = $allergeneRepository;
        $this->photoMenuRepository = $photoMenuRepository;
    }

    public function getAll(): array {
        return $this->menuRepository->findAll();
    }

    public function getAllThemes(): array {
        return $this->themeRepository->findAll();
    }

    public function getAllRegimes(): array {
        return $this->regimeRepository->findAll();
    }

    public function getAllDishes(): array {
        return $this->platRepository ? $this->platRepository->findAll() : [];
    }

    public function getAllAllergens(): array {
        return $this->allergeneRepository ? $this->allergeneRepository->findAll() : [];
    }

    public function getAvailablePhotos(): array {
        return $this->photoMenuRepository ? $this->photoMenuRepository->getAvailablePhotos() : [];
    }

    public function createMenu(
        string $title,
        string $description,
        int $themeId,
        int $regimeId,
        int $minPeople,
        float $unitPrice,
        int $daysBefore,
        int $stock,
        array $dishIds,
        string $mainPhotoPath
    ): bool {
        $trimmedTitle = trim($title);
        $trimmedDesc = trim($description);

        if (
            $trimmedTitle === '' ||
            mb_strlen($trimmedTitle, 'UTF-8') > 50 ||
            $trimmedDesc === '' ||
            mb_strlen($trimmedDesc, 'UTF-8') > 255 ||
            $themeId <= 0 ||
            $regimeId <= 0 ||
            $minPeople < 1 ||
            $unitPrice <= 0 ||
            $daysBefore < 1 ||
            $stock < 0
        ) {
            return false;
        }

        $menuId = $this->menuRepository->create(
            $trimmedTitle,
            $trimmedDesc,
            $themeId,
            $regimeId,
            $minPeople,
            round($unitPrice, 2),
            $daysBefore,
            $stock,
            $dishIds,
            trim($mainPhotoPath)
        );

        return $menuId > 0;
    }

    public function updateMenu(
        int $id,
        string $title,
        string $description,
        int $themeId,
        int $regimeId,
        int $minPeople,
        float $unitPrice,
        int $daysBefore,
        int $stock,
        array $dishIds,
        string $mainPhotoPath
    ): bool {
        $trimmedTitle = trim($title);
        $trimmedDesc = trim($description);

        if (
            $id <= 0 ||
            $trimmedTitle === '' ||
            mb_strlen($trimmedTitle, 'UTF-8') > 50 ||
            $trimmedDesc === '' ||
            mb_strlen($trimmedDesc, 'UTF-8') > 255 ||
            $themeId <= 0 ||
            $regimeId <= 0 ||
            $minPeople < 1 ||
            $unitPrice <= 0 ||
            $daysBefore < 1 ||
            $stock < 0
        ) {
            return false;
        }

        return $this->menuRepository->update(
            $id,
            $trimmedTitle,
            $trimmedDesc,
            $themeId,
            $regimeId,
            $minPeople,
            round($unitPrice, 2),
            $daysBefore,
            $stock,
            $dishIds,
            trim($mainPhotoPath)
        );
    }

    public function deleteMenu(int $id): bool {
        if ($id <= 0) {
            return false;
        }

        return $this->menuRepository->delete($id);
    }

    public function createDish(
        string $title,
        string $description,
        string $type,
        array $allergenIds
    ): bool {
        if ($this->platRepository === null) {
            return false;
        }

        $trimmedTitle = trim($title);
        $trimmedDesc = trim($description);
        $allowedTypes = ['Entrée', 'Plat', 'Dessert'];

        if (
            $trimmedTitle === '' ||
            mb_strlen($trimmedTitle, 'UTF-8') > 50 ||
            $trimmedDesc === '' ||
            mb_strlen($trimmedDesc, 'UTF-8') > 50 ||
            !in_array($type, $allowedTypes, true)
        ) {
            return false;
        }

        return $this->platRepository->create(
            $trimmedTitle,
            $trimmedDesc,
            $type,
            $allergenIds
        ) > 0;
    }

    public function updateDish(
        int $id,
        string $title,
        string $description,
        string $type,
        array $allergenIds
    ): bool {
        if ($this->platRepository === null || $id <= 0) {
            return false;
        }

        $trimmedTitle = trim($title);
        $trimmedDesc = trim($description);
        $allowedTypes = ['Entrée', 'Plat', 'Dessert'];

        if (
            $trimmedTitle === '' ||
            mb_strlen($trimmedTitle, 'UTF-8') > 50 ||
            $trimmedDesc === '' ||
            mb_strlen($trimmedDesc, 'UTF-8') > 50 ||
            !in_array($type, $allowedTypes, true)
        ) {
            return false;
        }

        return $this->platRepository->update(
            $id,
            $trimmedTitle,
            $trimmedDesc,
            $type,
            $allergenIds
        );
    }

    public function deleteDish(int $id): bool {
        if ($this->platRepository === null || $id <= 0) {
            return false;
        }

        return $this->platRepository->delete($id);
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
