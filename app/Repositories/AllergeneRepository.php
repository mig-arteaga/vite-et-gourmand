<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Allergene;
use PDO;

class AllergeneRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByDishId(int $dishId): array {
        $sql = '
            SELECT
                a.id_allergene,
                a.libelle
            FROM allergenes a
            INNER JOIN plats_allergenes pa
                ON a.id_allergene = pa.allergene
            WHERE pa.plat = :dishId
            ORDER BY a.id_allergene
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'dishId' => $dishId
        ]);

        $allergenes = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $allergenes[] = new Allergene(
                (int) $data['id_allergene'],
                $data['libelle']
            );
        }

        return $allergenes;
    }
}