<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Commande;
use DateTimeImmutable;
use PDO;

class CommandeRepository {
    private PDO $pdo;
    private UtilisateurRepository $utilisateurRepository;
    private MenuRepository $menuRepository;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->utilisateurRepository = new UtilisateurRepository($pdo);
        $this->menuRepository = new MenuRepository($pdo);
    }

    public function create(Commande $commande): int {
        $sql = '
            INSERT INTO commandes (
                order_token,
                utilisateur,
                menu,
                date_create,
                date_livraison,
                heure_livraison,
                adresse_livraison,
                ville_livraison,
                code_postal_livraison,
                qte_personnes,
                prix_menus,
                prix_livraison,
                reduction_groupe,
                frais_kilometriques,
                prix_total,
                pret_materiel,
                restitution,
                statut
            ) VALUES (
                :order_token,
                :utilisateur,
                :menu,
                :date_create,
                :date_livraison,
                :heure_livraison,
                :adresse_livraison,
                :ville_livraison,
                :code_postal_livraison,
                :qte_personnes,
                :prix_menus,
                :prix_livraison,
                :reduction_groupe,
                :frais_kilometriques,
                :prix_total,
                :pret_materiel,
                :restitution,
                :statut
            )
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'order_token' => $commande->getOrderToken(),
            'utilisateur' => $commande->getUser()->getId(),
            'menu' => $commande->getMenu()->getId(),
            'date_create' => $commande->getCreatedAt()->format('Y-m-d'),
            'date_livraison' => $commande->getDeliveryDate()->format('Y-m-d'),
            'heure_livraison' => $commande->getDeliveryTime(),
            'adresse_livraison' => $commande->getDeliveryAddress(),
            'ville_livraison' => $commande->getDeliveryCity(),
            'code_postal_livraison' => $commande->getDeliveryPostalCode(),
            'qte_personnes' => $commande->getPeopleCount(),
            'prix_menus' => $commande->getMenuPrice(),
            'prix_livraison' => $commande->getDeliveryPrice(),
            'reduction_groupe' => $commande->getGroupDiscount(),
            'frais_kilometriques' => $commande->getDistanceFee(),
            'prix_total' => $commande->getTotalPrice(),
            'pret_materiel' => (int) $commande->hasEquipmentLoan(),
            'restitution' => (int) $commande->isReturned(),
            'statut' => $commande->getStatus()
        ]);

        $commandeId = (int) $this->pdo->lastInsertId();

        $this->addStatusHistory($commandeId, $commande->getStatus());

        return $commandeId;
    }

    public function findIdByOrderToken(string $orderToken, int $userId): ?int {
        $sql = '
            SELECT id_commande
            FROM commandes
            WHERE order_token = :order_token
            AND utilisateur = :utilisateur
            LIMIT 1
        ';

        $statement = $this->pdo->prepare($sql);

        $statement->execute([
            'order_token' => $orderToken,
            'utilisateur' => $userId
        ]);

        $result = $statement->fetchColumn();

        return $result === false ? null : (int) $result;
    }

    public function findById(int $id): ?Commande {
        $sql = '
            SELECT
                id_commande,
                order_token,
                utilisateur,
                menu,
                date_create,
                date_livraison,
                heure_livraison,
                adresse_livraison,
                ville_livraison,
                code_postal_livraison,
                qte_personnes,
                prix_menus,
                prix_livraison,
                reduction_groupe,
                frais_kilometriques,
                prix_total,
                pret_materiel,
                restitution,
                statut,
                mode_contact,
                motif_annulation
            FROM commandes
            WHERE id_commande = :id
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            return null;
        }

        return $this->hydrate($data);
    }

    public function findAll(): array {
        $sql = '
            SELECT
                id_commande,
                order_token,
                utilisateur,
                menu,
                date_create,
                date_livraison,
                heure_livraison,
                adresse_livraison,
                ville_livraison,
                code_postal_livraison,
                qte_personnes,
                prix_menus,
                prix_livraison,
                reduction_groupe,
                frais_kilometriques,
                prix_total,
                pret_materiel,
                restitution,
                statut,
                mode_contact,
                motif_annulation
            FROM commandes
            ORDER BY
                CASE
                    WHEN statut = \'En attente\' THEN 0
                    WHEN statut = \'Accepté\' THEN 1
                    WHEN statut = \'En préparation\' THEN 2
                    WHEN statut = \'En cours de livraison\' THEN 3
                    WHEN statut = \'Livré\' THEN 4
                    WHEN statut = \'En attente du retour de matériel\' THEN 5
                    WHEN statut = \'Terminée\' THEN 6
                    ELSE 7
                END,
                date_livraison DESC,
                id_commande DESC
        ';

        $statement = $this->pdo->query($sql);

        $orders = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $order = $this->hydrate($data);
            if ($order !== null) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    /**
     * @return Commande[]
     */
    public function findByUserId(int $userId): array {
        $sql = '
            SELECT
                id_commande,
                order_token,
                utilisateur,
                menu,
                date_create,
                date_livraison,
                heure_livraison,
                adresse_livraison,
                ville_livraison,
                code_postal_livraison,
                qte_personnes,
                prix_menus,
                prix_livraison,
                reduction_groupe,
                frais_kilometriques,
                prix_total,
                pret_materiel,
                restitution,
                statut,
                mode_contact,
                motif_annulation
            FROM commandes
            WHERE utilisateur = :user_id
            ORDER BY
                CASE
                    WHEN statut = \'En attente\' THEN 0
                    WHEN statut = \'Accepté\' THEN 1
                    WHEN statut = \'En préparation\' THEN 2
                    WHEN statut = \'En cours de livraison\' THEN 3
                    WHEN statut = \'Livré\' THEN 4
                    WHEN statut = \'En attente du retour de matériel\' THEN 5
                    WHEN statut = \'Terminée\' THEN 6
                    ELSE 7
                END,
                date_livraison DESC,
                id_commande DESC
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['user_id' => $userId]);

        $orders = [];

        while ($data = $statement->fetch(PDO::FETCH_ASSOC)) {
            $order = $this->hydrate($data);
            if ($order !== null) {
                $orders[] = $order;
            }
        }

        return $orders;
    }

    public function updateByClient(
        int $orderId,
        int $userId,
        string $deliveryDate,
        string $deliveryTime,
        string $deliveryAddress,
        string $deliveryCity,
        string $deliveryPostalCode,
        int $peopleCount,
        float $menuPrice,
        float $deliveryPrice,
        float $groupDiscount,
        float $distanceFee,
        float $totalPrice,
        bool $equipmentLoan
    ): bool {
        $sql = '
            UPDATE commandes
            SET
                date_livraison = :date_livraison,
                heure_livraison = :heure_livraison,
                adresse_livraison = :adresse_livraison,
                ville_livraison = :ville_livraison,
                code_postal_livraison = :code_postal_livraison,
                qte_personnes = :qte_personnes,
                prix_menus = :prix_menus,
                prix_livraison = :prix_livraison,
                reduction_groupe = :reduction_groupe,
                frais_kilometriques = :frais_kilometriques,
                prix_total = :prix_total,
                pret_materiel = :pret_materiel
            WHERE id_commande = :id
              AND utilisateur = :user_id
              AND statut = \'En attente\'
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'date_livraison' => $deliveryDate,
            'heure_livraison' => $deliveryTime,
            'adresse_livraison' => $deliveryAddress,
            'ville_livraison' => $deliveryCity,
            'code_postal_livraison' => $deliveryPostalCode,
            'qte_personnes' => $peopleCount,
            'prix_menus' => $menuPrice,
            'prix_livraison' => $deliveryPrice,
            'reduction_groupe' => $groupDiscount,
            'frais_kilometriques' => $distanceFee,
            'prix_total' => $totalPrice,
            'pret_materiel' => $equipmentLoan ? 1 : 0,
            'id' => $orderId,
            'user_id' => $userId
        ]);
    }

    public function cancelByClient(int $orderId, int $userId): bool {
        $sql = '
            UPDATE commandes
            SET statut = \'Annulée\',
                mode_contact = \'Espace client\',
                motif_annulation = \'Annulation effectuée par le client depuis son espace personnel\'
            WHERE id_commande = :id
              AND utilisateur = :user_id
              AND statut = \'En attente\'
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id' => $orderId,
            'user_id' => $userId
        ]);

        return $statement->rowCount() === 1;
    }

    public function addStatusHistory(int $commandeId, string $status): void {
        $sql = '
            INSERT INTO commande_suivi (commande, statut)
            VALUES (:commande, :statut)
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'commande' => $commandeId,
            'statut' => $status
        ]);
    }

    public function findStatusHistory(int $commandeId): array {
        $sql = '
            SELECT
                statut,
                date_modification
            FROM commande_suivi
            WHERE commande = :commande
            ORDER BY date_modification ASC, id_suivi ASC
        ';

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['commande' => $commandeId]);

        $history = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $history[] = [
                'statut' => $row['statut'],
                'date_modification' => new DateTimeImmutable($row['date_modification'])
            ];
        }

        return $history;
    }

    public function updateStatus(int $id, string $status, bool $returned): bool {
        $sql = '
            UPDATE commandes
            SET statut = :statut,
                restitution = :restitution
            WHERE id_commande = :id
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'statut' => $status,
            'restitution' => (int) $returned,
            'id' => $id
        ]);
    }

    public function cancelByEmployee(
        int $id,
        string $contactMode,
        string $reason
    ): bool {
        $sql = '
            UPDATE commandes
            SET statut = \'Annulée\',
                mode_contact = :mode_contact,
                motif_annulation = :motif_annulation
            WHERE id_commande = :id
        ';

        $statement = $this->pdo->prepare($sql);

        return $statement->execute([
            'mode_contact' => $contactMode,
            'motif_annulation' => $reason,
            'id' => $id
        ]);
    }

    /**
     * Calcule le nombre de commandes et le chiffre d'affaires par menu (hors commandes annulées),
     * avec filtres optionnels par menu et par plage de dates de livraison.
     *
     * @return array<int, array{
     *     id_menu: int,
     *     titre: string,
     *     prix_personne: float,
     *     nb_commandes: int,
     *     total_personnes: int,
     *     chiffre_affaires: float
     * }>
     */
    public function getRevenueStats(
        ?int $menuId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $joinConditions = ["c.menu = m.id_menu", "c.statut <> 'Annulée'"];
        $whereConditions = [];
        $params = [];

        if ($startDate !== null && $startDate !== '') {
            $joinConditions[] = 'c.date_livraison >= :start_date';
            $params['start_date'] = $startDate;
        }

        if ($endDate !== null && $endDate !== '') {
            $joinConditions[] = 'c.date_livraison <= :end_date';
            $params['end_date'] = $endDate;
        }

        if ($menuId !== null && $menuId > 0) {
            $whereConditions[] = 'm.id_menu = :menu_id';
            $params['menu_id'] = $menuId;
        }

        $joinClause = implode(' AND ', $joinConditions);
        $whereClause = !empty($whereConditions)
            ? 'WHERE ' . implode(' AND ', $whereConditions)
            : '';

        $sql = "
            SELECT
                m.id_menu,
                m.titre,
                m.prix_personne,
                COUNT(c.id_commande) AS nb_commandes,
                COALESCE(SUM(c.qte_personnes), 0) AS total_personnes,
                COALESCE(SUM(c.prix_total), 0) AS chiffre_affaires
            FROM menus m
            LEFT JOIN commandes c
                ON {$joinClause}
            {$whereClause}
            GROUP BY m.id_menu, m.titre, m.prix_personne
            ORDER BY chiffre_affaires DESC, nb_commandes DESC, m.id_menu ASC
        ";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        $stats = [];

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $stats[] = [
                'id_menu' => (int) $row['id_menu'],
                'titre' => (string) $row['titre'],
                'prix_personne' => (float) $row['prix_personne'],
                'nb_commandes' => (int) $row['nb_commandes'],
                'total_personnes' => (int) $row['total_personnes'],
                'chiffre_affaires' => (float) $row['chiffre_affaires']
            ];
        }

        return $stats;
    }

    private function hydrate(array $data): ?Commande {
        $user = $this->utilisateurRepository->findById(
            (int) $data['utilisateur']
        );

        $menu = $this->menuRepository->findById(
            (int) $data['menu']
        );

        if ($user === null || $menu === null) {
            return null;
        }

        $orderId = (int) $data['id_commande'];
        $history = $this->findStatusHistory($orderId);

        return new Commande(
            $orderId,
            $data['order_token'],
            $user,
            $menu,
            new DateTimeImmutable($data['date_create']),
            new DateTimeImmutable($data['date_livraison']),
            substr($data['heure_livraison'], 0, 5),
            $data['adresse_livraison'],
            $data['ville_livraison'],
            $data['code_postal_livraison'],
            (int) $data['qte_personnes'],
            (float) $data['prix_menus'],
            (float) $data['prix_livraison'],
            (float) $data['reduction_groupe'],
            (float) $data['frais_kilometriques'],
            (float) $data['prix_total'],
            (bool) $data['pret_materiel'],
            (bool) $data['restitution'],
            $data['statut'],
            $data['mode_contact'] ?? null,
            $data['motif_annulation'] ?? null,
            $history
        );
    }
}
