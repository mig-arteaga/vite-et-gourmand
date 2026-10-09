<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Menu;

class MenuService {
    public function getCourseDescription(Menu $menu): string {
        $hasAppetizer = false;
        $hasMainCourse = false;
        $hasDessert = false;

        foreach ($menu->getDishes() as $dish) {
            switch ($dish->getType()) {
                case 'Entrée':
                    $hasAppetizer = true;
                    break;

                case 'Plat':
                    $hasMainCourse = true;
                    break;

                case 'Dessert':
                    $hasDessert = true;
                    break;
            }
        }

        if ($hasAppetizer && $hasMainCourse && $hasDessert) {
            return 'Entrée + plat + dessert';
        }

        if ($hasAppetizer && $hasMainCourse) {
            return 'Entrée + plat';
        }

        if ($hasMainCourse && $hasDessert) {
            return 'Plat + dessert';
        }

        return '';
    }

    public function getAllergenCount(Menu $menu): int {
        $allergens = [];

        foreach ($menu->getDishes() as $dish) {
            foreach ($dish->getAllergens() as $allergen) {
                $allergens[$allergen->getId()] = true;
            }
        }

        return count($allergens);
    }
}