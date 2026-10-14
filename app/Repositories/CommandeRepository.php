<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Commande;
use PDO;

class CommandeRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
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

        return (int) $this->pdo->lastInsertId();
    }

    public function findIdByOrderToken(string $orderToken, int $userId): ?int
    {
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

        $id = $statement->fetchColumn();

        return $id === false ? null : (int) $id;
    }
}