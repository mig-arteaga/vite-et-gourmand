<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Role;
use App\Models\Utilisateur;
use PDO;

class UtilisateurRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?Utilisateur {
        $sql = '
            SELECT
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.email,
                u.mot_de_passe,
                u.telephone,
                u.adresse,
                u.code_postal,
                u.ville,
                u.photo,
                r.id_role,
                r.libelle AS role_label
            FROM utilisateurs u
            INNER JOIN roles r
                ON u.role = r.id_role
            WHERE u.email = :email
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'email' => $email
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findById(int $id): ?Utilisateur {
        $sql = '
            SELECT
                u.id_utilisateur,
                u.nom,
                u.prenom,
                u.email,
                u.mot_de_passe,
                u.telephone,
                u.adresse,
                u.code_postal,
                u.ville,
                u.photo,
                r.id_role,
                r.libelle AS role_label
            FROM utilisateurs u
            INNER JOIN roles r
                ON u.role = r.id_role
            WHERE u.id_utilisateur = :id
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'id' => $id
        ]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->hydrate($data);
    }

    private function hydrate(array $data): Utilisateur {
        $role = new Role(
            (int) $data['id_role'],
            $data['role_label']
        );

        return new Utilisateur(
            (int) $data['id_utilisateur'],
            $data['nom'],
            $data['prenom'],
            $data['email'],
            $data['mot_de_passe'],
            $data['telephone'],
            $data['adresse'],
            (int) $data['code_postal'],
            $data['ville'],
            $data['photo'],
            $role
        );
    }
}