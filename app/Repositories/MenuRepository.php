<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Menu;
use App\Models\Regime;
use App\Models\Theme;
use PDO;

class MenuRepository {
    private PDO $pdo;
    private PhotoMenuRepository $photoMenuRepository;
    private PlatRepository $platRepository;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->photoMenuRepository = new PhotoMenuRepository($pdo);
        $this->platRepository = new PlatRepository($pdo);
    }

    public function findById(int $id): ?Menu {
        $sql = '
            SELECT
                m.id_menu,
                m.titre,
                m.description,
                m.min_personnes,
                m.prix_personne,
                m.jours_avant,
                m.stock,
                t.id_theme,
                t.libelle AS theme_label,
                r.id_regime,
                r.libelle AS regime_label
            FROM menus m
            INNER JOIN themes t
                ON m.theme = t.id_theme
            INNER JOIN regimes r
                ON m.regime = r.id_regime
            WHERE m.id_menu = :id
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAll(): array {
        $sql = '
            SELECT
                m.id_menu,
                m.titre,
                m.description,
                m.min_personnes,
                m.prix_personne,
                m.jours_avant,
                m.stock,
                t.id_theme,
                t.libelle AS theme_label,
                r.id_regime,
                r.libelle AS regime_label
            FROM menus m
            INNER JOIN themes t
                ON m.theme = t.id_theme
            INNER JOIN regimes r
                ON m.regime = r.id_regime
            ORDER BY m.id_menu
        ';

        $statement = $this->pdo->query($sql);

        $menus = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $menus[] = $this->hydrate($data);
        }

        return $menus;
    }

    public function findByFilters(array $filters): array {
        $sql = '
            SELECT
                m.id_menu,
                m.titre,
                m.description,
                m.min_personnes,
                m.prix_personne,
                m.jours_avant,
                m.stock,
                t.id_theme,
                t.libelle AS theme_label,
                r.id_regime,
                r.libelle AS regime_label
            FROM menus m
            INNER JOIN themes t
                ON m.theme = t.id_theme
            INNER JOIN regimes r
                ON m.regime = r.id_regime
            WHERE 1 = 1
        ';

        $parameters = [];

        if (!empty($filters['minPrice'])) {
            $sql .= ' AND m.prix_personne >= :minPrice';
            $parameters['minPrice'] = $filters['minPrice'];
        }

        if (!empty($filters['maxPrice'])) {
            $sql .= ' AND m.prix_personne <= :maxPrice';
            $parameters['maxPrice'] = $filters['maxPrice'];
        }

        if (!empty($filters['theme'])) {
            $sql .= ' AND t.libelle = :theme';
            $parameters['theme'] = $filters['theme'];
        }

        if (!empty($filters['diet'])) {
            $sql .= ' AND r.libelle = :diet';
            $parameters['diet'] = $filters['diet'];
        }

        if (!empty($filters['people'])) {
            $sql .= ' AND m.min_personnes <= :people';
            $parameters['people'] = $filters['people'];
        }

        $sql .= ' ORDER BY m.id_menu';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);

        $menus = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $menus[] = $this->hydrate($data);
        }

        return $menus;
    }

    private function hydrate(array $data): Menu {
        $theme = new Theme(
            (int) $data['id_theme'],
            $data['theme_label']
        );

        $regime = new Regime(
            (int) $data['id_regime'],
            $data['regime_label']
        );

        $dishes = $this->platRepository->findByMenuId(
            (int) $data['id_menu']
        );

        $photos = $this->photoMenuRepository->findByMenuId(
            (int) $data['id_menu']
        );

        return new Menu(
            (int) $data['id_menu'],
            $data['titre'],
            $data['description'],
            $theme,
            $regime,
            (int) $data['min_personnes'],
            (float) $data['prix_personne'],
            (int) $data['jours_avant'],
            (int) $data['stock'],
            $dishes,
            $photos
        );
    }

    public function getPriceRange(): array {
        $sql = '
            SELECT
                MIN(prix_personne) AS minPrice,
                MAX(prix_personne) AS maxPrice
            FROM menus
        ';

        $statement = $this->pdo->query($sql);
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        return [
            'minPrice' => (float) $data['minPrice'],
            'maxPrice' => (float) $data['maxPrice']
        ];
    }

    public function getPeopleRange(): array {
        $sql = '
            SELECT
                MIN(min_personnes) AS minPeople,
                MAX(min_personnes) AS maxPeople
            FROM menus
        ';

        $statement = $this->pdo->query($sql);
        $data = $statement->fetch(PDO::FETCH_ASSOC);

        return [
            'minPeople' => (int) $data['minPeople'],
            'maxPeople' => (int) $data['maxPeople']
        ];
    }

    public function reserveStock(int $menuId, int $people): bool {
        $sql = '
            UPDATE menus
            SET stock = stock - :people
            WHERE id_menu = :menuId
            AND stock >= :people
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'menuId' => $menuId,
            'people' => $people
        ]);

        return $statement->rowCount() === 1;
    }

    public function restoreStock(int $menuId, int $people): bool {
        $sql = '
            UPDATE menus
            SET stock = stock + :people
            WHERE id_menu = :menuId
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'menuId' => $menuId,
            'people' => $people
        ]);

        return $statement->rowCount() === 1;
    }

    public function create(
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
    ): int {
        $sql = '
            INSERT INTO menus (
                titre,
                description,
                theme,
                regime,
                min_personnes,
                prix_personne,
                jours_avant,
                stock
            ) VALUES (
                :titre,
                :description,
                :theme,
                :regime,
                :min_personnes,
                :prix_personne,
                :jours_avant,
                :stock
            )
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'titre' => $title,
            'description' => $description,
            'theme' => $themeId,
            'regime' => $regimeId,
            'min_personnes' => $minPeople,
            'prix_personne' => $unitPrice,
            'jours_avant' => $daysBefore,
            'stock' => $stock
        ]);

        $menuId = (int) $this->pdo->lastInsertId();

        $this->syncDishes($menuId, $dishIds);
        $this->photoMenuRepository->replaceMenuPhotos($menuId, $mainPhotoPath);

        return $menuId;
    }

    public function update(
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
        $sql = '
            UPDATE menus
            SET titre = :titre,
                description = :description,
                theme = :theme,
                regime = :regime,
                min_personnes = :min_personnes,
                prix_personne = :prix_personne,
                jours_avant = :jours_avant,
                stock = :stock
            WHERE id_menu = :id
        ';

        $statement = $this->pdo->prepare($sql);
        $updated = $statement->execute([
            'titre' => $title,
            'description' => $description,
            'theme' => $themeId,
            'regime' => $regimeId,
            'min_personnes' => $minPeople,
            'prix_personne' => $unitPrice,
            'jours_avant' => $daysBefore,
            'stock' => $stock,
            'id' => $id
        ]);

        if (!$updated) {
            return false;
        }

        $this->syncDishes($id, $dishIds);
        $this->photoMenuRepository->replaceMenuPhotos($id, $mainPhotoPath);

        return true;
    }

    public function delete(int $id): bool {
        // Vérifier si des commandes référencent déjà ce menu
        $checkStmt = $this->pdo->prepare('SELECT COUNT(*) FROM commandes WHERE menu = :id');
        $checkStmt->execute(['id' => $id]);
        $ordersCount = (int) $checkStmt->fetchColumn();

        if ($ordersCount > 0) {
            // Si des commandes historiques existent, on passe le stock à 0 pour préserver l'intégrité référentielle
            $zeroStmt = $this->pdo->prepare('UPDATE menus SET stock = 0 WHERE id_menu = :id');
            return $zeroStmt->execute(['id' => $id]);
        }

        $delDishes = $this->pdo->prepare('DELETE FROM menus_plats WHERE menu = :id');
        $delDishes->execute(['id' => $id]);

        $this->photoMenuRepository->deleteByMenuId($id);

        $delMenu = $this->pdo->prepare('DELETE FROM menus WHERE id_menu = :id');

        return $delMenu->execute(['id' => $id]);
    }

    private function syncDishes(int $menuId, array $dishIds): void {
        $del = $this->pdo->prepare('DELETE FROM menus_plats WHERE menu = :menu');
        $del->execute(['menu' => $menuId]);

        $ins = $this->pdo->prepare('
            INSERT INTO menus_plats (menu, plat)
            VALUES (:menu, :plat)
        ');

        $uniqueIds = array_unique(array_map('intval', $dishIds));

        foreach ($uniqueIds as $dishId) {
            if ($dishId > 0) {
                $ins->execute([
                    'menu' => $menuId,
                    'plat' => $dishId
                ]);
            }
        }
    }
}