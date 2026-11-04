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
                u.actif,
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
                u.actif,
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

    /**
     * @return Utilisateur[]
     */
    public function findEmployees(): array {
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
                u.actif,
                r.id_role,
                r.libelle AS role_label
            FROM utilisateurs u
            INNER JOIN roles r
                ON u.role = r.id_role
            WHERE r.libelle = :role_label
            ORDER BY u.id_utilisateur DESC
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'role_label' => 'Employé'
        ]);

        $employees = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $employees[] = $this->hydrate($row);
        }

        return $employees;
    }

    public function createEmployee(
        string $lastName,
        string $firstName,
        string $email,
        string $passwordHash,
        string $phone
    ): int {
        $roleSql = '
            SELECT id_role
            FROM roles
            WHERE libelle = :label
            LIMIT 1
        ';

        $roleStmt = $this->pdo->prepare($roleSql);
        $roleStmt->execute(['label' => 'Employé']);
        $roleId = (int) ($roleStmt->fetchColumn() ?: 2);

        $sql = '
            INSERT INTO utilisateurs (
                nom,
                prenom,
                email,
                mot_de_passe,
                telephone,
                adresse,
                code_postal,
                ville,
                photo,
                role,
                actif
            ) VALUES (
                :last_name,
                :first_name,
                :email,
                :password,
                :phone,
                :address,
                :postal_code,
                :city,
                :photo,
                :role,
                1
            )
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'password' => $passwordHash,
            'phone' => $phone,
            'address' => '3 Place Notre Dame',
            'postal_code' => 38000,
            'city' => 'Grenoble',
            'photo' => 'images/profile-3.jpg',
            'role' => $roleId
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateEmployeeActiveStatus(int $userId, bool $active): bool {
        $sql = '
            UPDATE utilisateurs u
            INNER JOIN roles r
                ON u.role = r.id_role
            SET u.actif = :active
            WHERE u.id_utilisateur = :id
              AND r.libelle = :role_label
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'active' => $active ? 1 : 0,
            'id' => $userId,
            'role_label' => 'Employé'
        ]);
    }

    public function updateProfile(
        int $userId,
        string $lastName,
        string $firstName,
        string $email,
        string $phone,
        string $address,
        int $postalCode,
        string $city
    ): bool {
        $sql = '
            UPDATE utilisateurs
            SET
                nom = :last_name,
                prenom = :first_name,
                email = :email,
                telephone = :phone,
                adresse = :address,
                code_postal = :postal_code,
                ville = :city
            WHERE id_utilisateur = :id
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'last_name' => $lastName,
            'first_name' => $firstName,
            'email' => $email,
            'phone' => $phone,
            'address' => $address,
            'postal_code' => $postalCode,
            'city' => $city,
            'id' => $userId
        ]);
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
            $role,
            isset($data['actif']) ? (bool) $data['actif'] : true
        );
    }
}