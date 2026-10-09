<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Role;
use PDO;

class RoleRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findById(int $id): ?Role {
        $sql = '
            SELECT
                id_role,
                libelle
            FROM roles
            WHERE id_role = :id
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return new Role(
            (int) $data['id_role'],
            $data['libelle']
        );
    }
}