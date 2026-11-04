<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Avis;
use DateTimeImmutable;
use PDO;

class AvisRepository {
    private PDO $pdo;
    private UtilisateurRepository $utilisateurRepository;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->utilisateurRepository = new UtilisateurRepository($pdo);
    }

    public function findByStatus(string $status): array {
        $sql = '
            SELECT
                id_avis,
                utilisateur,
                commande,
                date_avis,
                note,
                message,
                statut
            FROM avis
            WHERE statut = :status
            ORDER BY date_avis
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'status' => $status
        ]);

        return $this->hydrateAll($statement);
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_avis,
                utilisateur,
                commande,
                date_avis,
                note,
                message,
                statut
            FROM avis
            ORDER BY
                CASE
                    WHEN statut = \'En attente\' THEN 0
                    WHEN statut = \'Validé\' THEN 1
                    ELSE 2
                END,
                date_avis DESC,
                id_avis DESC
        ';

        $statement = $this->pdo->query($sql);

        return $this->hydrateAll($statement);
    }

    public function updateStatus(int $id, string $status): bool {
        $sql = '
            UPDATE avis
            SET statut = :status
            WHERE id_avis = :id
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'status' => $status,
            'id' => $id
        ]);
    }

    public function findByUserId(int $userId): array {
        $sql = '
            SELECT
                id_avis,
                utilisateur,
                commande,
                date_avis,
                note,
                message,
                statut
            FROM avis
            WHERE utilisateur = :user_id
            ORDER BY date_avis DESC, id_avis DESC
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['user_id' => $userId]);

        return $this->hydrateAll($statement);
    }

    public function existsForOrder(int $orderId): bool {
        $sql = '
            SELECT COUNT(*)
            FROM avis
            WHERE commande = :order_id
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['order_id' => $orderId]);

        return ((int) $statement->fetchColumn()) > 0;
    }

    public function create(int $userId, int $orderId, int $rating, string $message): int {
        $sql = '
            INSERT INTO avis (
                utilisateur,
                commande,
                date_avis,
                note,
                message,
                statut
            ) VALUES (
                :user_id,
                :order_id,
                CURRENT_DATE,
                :rating,
                :message,
                \'En attente\'
            )
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'order_id' => $orderId,
            'rating' => $rating,
            'message' => $message
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    private function hydrateAll(\PDOStatement $statement): array {
        $reviews = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $user = $this->utilisateurRepository->findById(
                (int) $data['utilisateur']
            );

            if ($user === null) {
                continue;
            }

            $reviews[] = new Avis(
                (int) $data['id_avis'],
                $user,
                new DateTimeImmutable($data['date_avis']),
                (int) $data['note'],
                $data['message'],
                $data['statut'],
                isset($data['commande']) && $data['commande'] !== null ? (int) $data['commande'] : null
            );
        }

        return $reviews;
    }
}
