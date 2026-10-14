<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Plat;
use PDO;

class PlatRepository {
    private PDO $pdo;
    private AllergeneRepository $allergeneRepository;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->allergeneRepository = new AllergeneRepository($pdo);
    }

    public function findByMenuId(int $menuId): array {
        $sql = '
            SELECT
                p.id_plat,
                p.titre,
                p.description,
                p.type
            FROM plats p
            INNER JOIN menus_plats mp
                ON p.id_plat = mp.plat
            WHERE mp.menu = :menuId
            ORDER BY p.id_plat
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'menuId' => $menuId
        ]);

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