<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Plat;
use PDO;

class PlatRepository {
    private PDO $pdo;
    private AllergeneRepository $allergeneRepository;

    /** @var ?array<int, Plat[]> */
    private static ?array $byMenuCache = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->allergeneRepository = new AllergeneRepository($pdo);
    }

    public static function clearCache(): void {
        self::$byMenuCache = null;
        AllergeneRepository::clearCache();
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_plat,
                titre,
                description,
                type
            FROM plats
            ORDER BY
                CASE
                    WHEN type = \'Entrée\' THEN 0
                    WHEN type = \'Plat\' THEN 1
                    ELSE 2
                END,
                id_plat ASC
        ';

        $statement = $this->pdo->query($sql);

        return $this->hydrateAll($statement);
    }

    public function findByMenuId(int $menuId): array {
        if (self::$byMenuCache === null) {
            self::$byMenuCache = [];

            $sql = '
                SELECT
                    mp.menu,
                    p.id_plat,
                    p.titre,
                    p.description,
                    p.type
                FROM plats p
                INNER JOIN menus_plats mp
                    ON p.id_plat = mp.plat
                ORDER BY mp.menu, p.id_plat
            ';

            $statement = $this->pdo->query($sql);

            while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
                $mId = (int) $data['menu'];
                $dishId = (int) $data['id_plat'];
                $allergenes = $this->allergeneRepository->findByDishId($dishId);

                self::$byMenuCache[$mId][] = new Plat(
                    $dishId,
                    $data['titre'],
                    $data['description'],
                    $data['type'],
                    $allergenes
                );
            }
        }

        return self::$byMenuCache[$menuId] ?? [];
    }

    public function create(
        string $title,
        string $description,
        string $type,
        array $allergenIds
    ): int {
        $sql = '
            INSERT INTO plats (titre, description, type)
            VALUES (:titre, :description, :type)
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'titre' => $title,
            'description' => $description,
            'type' => $type
        ]);

        $dishId = (int) $this->pdo->lastInsertId();

        $this->syncAllergens($dishId, $allergenIds);
        self::clearCache();

        return $dishId;
    }

    public function update(
        int $id,
        string $title,
        string $description,
        string $type,
        array $allergenIds
    ): bool {
        $sql = '
            UPDATE plats
            SET titre = :titre,
                description = :description,
                type = :type
            WHERE id_plat = :id
        ';

        $statement = $this->pdo->prepare($sql);
        $updated = $statement->execute([
            'titre' => $title,
            'description' => $description,
            'type' => $type,
            'id' => $id
        ]);

        if (!$updated) {
            return false;
        }

        $this->syncAllergens($id, $allergenIds);
        self::clearCache();

        return true;
    }

    public function delete(int $id): bool {
        $delAllergens = $this->pdo->prepare('DELETE FROM plats_allergenes WHERE plat = :id');
        $delAllergens->execute(['id' => $id]);

        $delMenus = $this->pdo->prepare('DELETE FROM menus_plats WHERE plat = :id');
        $delMenus->execute(['id' => $id]);

        $delPlat = $this->pdo->prepare('DELETE FROM plats WHERE id_plat = :id');
        $deleted = $delPlat->execute(['id' => $id]);

        self::clearCache();

        return $deleted;
    }

    private function syncAllergens(int $dishId, array $allergenIds): void {
        $del = $this->pdo->prepare('DELETE FROM plats_allergenes WHERE plat = :plat');
        $del->execute(['plat' => $dishId]);

        $ins = $this->pdo->prepare('
            INSERT INTO plats_allergenes (plat, allergene)
            VALUES (:plat, :allergene)
        ');

        $uniqueIds = array_unique(array_map('intval', $allergenIds));

        foreach ($uniqueIds as $allergenId) {
            if ($allergenId > 0) {
                $ins->execute([
                    'plat' => $dishId,
                    'allergene' => $allergenId
                ]);
            }
        }
    }

    private function hydrateAll(\PDOStatement $statement): array {
        $dishes = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $allergenes = $this->allergeneRepository->findByDishId(
                (int) $data['id_plat']
            );

            $dishes[] = new Plat(
                (int) $data['id_plat'],
                $data['titre'],
                $data['description'],
                $data['type'],
                $allergenes
            );
        }

        return $dishes;
    }
}
