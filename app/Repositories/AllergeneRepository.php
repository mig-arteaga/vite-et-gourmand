<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Allergene;
use PDO;

class AllergeneRepository {
    private PDO $pdo;

    /** @var ?array<int, Allergene[]> */
    private static ?array $byDishCache = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public static function clearCache(): void {
        self::$byDishCache = null;
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_allergene,
                libelle
            FROM allergenes
            ORDER BY id_allergene
        ';

        $statement = $this->pdo->query($sql);

        $allergenes = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $allergenes[] = new Allergene(
                (int) $data['id_allergene'],
                $data['libelle']
            );
        }

        return $allergenes;
    }

    public function findByDishId(int $dishId): array {
        if (self::$byDishCache === null) {
            self::$byDishCache = [];

            $sql = '
                SELECT
                    pa.plat,
                    a.id_allergene,
                    a.libelle
                FROM allergenes a
                INNER JOIN plats_allergenes pa
                    ON a.id_allergene = pa.allergene
                ORDER BY pa.plat, a.id_allergene
            ';

            $statement = $this->pdo->query($sql);

            while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
                $platId = (int) $data['plat'];
                self::$byDishCache[$platId][] = new Allergene(
                    (int) $data['id_allergene'],
                    $data['libelle']
                );
            }
        }

        return self::$byDishCache[$dishId] ?? [];
    }
}
