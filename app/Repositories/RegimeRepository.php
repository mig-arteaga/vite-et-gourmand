<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Regime;
use PDO;

class RegimeRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Regime {
        $sql = '
            SELECT id_regime, libelle
            FROM regimes
            WHERE id_regime = :id
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return new Regime(
            (int) $data['id_regime'],
            $data['libelle']
        );
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_regime,
                libelle
            FROM regimes
            ORDER BY id_regime
        ';

        $statement = $this->pdo->query($sql);

        $diets = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $diets[] = new Regime(
                (int) $data['id_regime'],
                $data['libelle']
            );
        }

        return $diets;
    }
}