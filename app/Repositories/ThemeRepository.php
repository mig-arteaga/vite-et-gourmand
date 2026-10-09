<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Theme;
use PDO;

class ThemeRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Theme {
        $sql = '
            SELECT id_theme, libelle
            FROM themes
            WHERE id_theme = :id
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return new Theme(
            (int) $data['id_theme'],
            $data['libelle']
        );
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_theme,
                libelle
            FROM themes
            ORDER BY id_theme
        ';

        $statement = $this->pdo->query($sql);

        $themes = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $themes[] = new Theme(
                (int) $data['id_theme'],
                $data['libelle']
            );
        }

        return $themes;
    }
}