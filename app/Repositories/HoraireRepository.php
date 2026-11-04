<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Horaire;
use PDO;

class HoraireRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @return Horaire[]
     */
    public function findAll(): array {
        $sql = '
            SELECT
                id_horaire,
                jour,
                heure_ouverture,
                heure_fermeture,
                ferme
            FROM horaires
            ORDER BY id_horaire ASC
        ';

        $statement = $this->pdo->query($sql);
        $horaires = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $horaires[] = new Horaire(
                (int) $row['id_horaire'],
                (string) $row['jour'],
                $row['heure_ouverture'] !== null ? (string) $row['heure_ouverture'] : null,
                $row['heure_fermeture'] !== null ? (string) $row['heure_fermeture'] : null,
                (bool) $row['ferme']
            );
        }

        return $horaires;
    }

    public function update(
        int $id,
        ?string $openingTime,
        ?string $closingTime,
        bool $closed
    ): bool {
        $sql = '
            UPDATE horaires
            SET
                heure_ouverture = :opening,
                heure_fermeture = :closing,
                ferme = :closed
            WHERE id_horaire = :id
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'opening' => $closed ? null : $openingTime,
            'closing' => $closed ? null : $closingTime,
            'closed' => $closed ? 1 : 0,
            'id' => $id
        ]);
    }
}

