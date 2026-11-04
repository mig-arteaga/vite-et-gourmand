<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\PhotoMenu;
use PDO;

class PhotoMenuRepository {
    private PDO $pdo;

    private const AVAILABLE_PHOTOS = [
        'images/menu-1-1.jpg',
        'images/menu-1-2.jpg',
        'images/menu-1-3.jpg',
        'images/menu-1-4.jpg',
        'images/menu-1-5.jpg',
        'images/menu-1-6.jpg',
        'images/menu-1-7.jpg',
        'images/menu-1-8.jpg',
        'images/menu-1-9.jpg',
        'images/menu-1-10.jpg'
    ];

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAvailablePhotos(): array {
        return self::AVAILABLE_PHOTOS;
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

    public function replaceMenuPhotos(int $menuId, string $mainPhotoPath): void {
        if (!in_array($mainPhotoPath, self::AVAILABLE_PHOTOS, true)) {
            $mainPhotoPath = self::AVAILABLE_PHOTOS[0];
        }

        $this->deleteByMenuId($menuId);

        $startIndex = array_search($mainPhotoPath, self::AVAILABLE_PHOTOS, true);
        if ($startIndex === false) {
            $startIndex = 0;
        }

        $totalPhotos = count(self::AVAILABLE_PHOTOS);

        $sql = '
            INSERT INTO photos_menu (menu, chemin, ordre)
            VALUES (:menu, :chemin, :ordre)
        ';

        $statement = $this->pdo->prepare($sql);

        for ($order = 1; $order <= 5; $order++) {
            $photoIndex = ($startIndex + $order - 1) % $totalPhotos;
            $statement->execute([
                'menu' => $menuId,
                'chemin' => self::AVAILABLE_PHOTOS[$photoIndex],
                'ordre' => $order
            ]);
        }
    }

    public function deleteByMenuId(int $menuId): void {
        $statement = $this->pdo->prepare('DELETE FROM photos_menu WHERE menu = :menuId');
        $statement->execute(['menuId' => $menuId]);
    }
}
