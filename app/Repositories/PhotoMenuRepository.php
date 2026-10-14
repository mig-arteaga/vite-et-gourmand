<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PhotoMenu;
use PDO;

class PhotoMenuRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByMenuId(int $menuId): array {
        $sql = '
            SELECT
                id_photo,
                chemin,
                ordre
            FROM photos_menu
            WHERE menu = :menuId
            ORDER BY ordre
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'menuId' => $menuId
        ]);

        $photos = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $photos[] = new PhotoMenu(
                (int) $data['id_photo'],
                $data['chemin'],
                (int) $data['ordre']
            );
        }

        return $photos;
    }
}