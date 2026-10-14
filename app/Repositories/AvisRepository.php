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
                $data['statut']
            );
        }

        return $reviews;
    }
}