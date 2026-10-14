-- Migration 001 : Mise à jour de la table commandes
-- Étape 1 : Ajouter les nouvelles colonnes

ALTER TABLE commandes
    ADD COLUMN heure_livraison TIME NULL AFTER date_livraison,
    ADD COLUMN adresse_livraison VARCHAR(255) NULL AFTER heure_livraison,
    ADD COLUMN ville_livraison VARCHAR(100) NULL AFTER adresse_livraison,
    ADD COLUMN code_postal_livraison VARCHAR(20) NULL AFTER ville_livraison,
    ADD COLUMN reduction_groupe DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER prix_livraison,
    ADD COLUMN frais_kilometriques DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER reduction_groupe,
    ADD COLUMN prix_total DECIMAL(10,2) NULL AFTER frais_kilometriques;


-- Étape 2 : Renseigner les informations de livraison

UPDATE commandes c
INNER JOIN utilisateurs u
    ON c.utilisateur = u.id_utilisateur
SET
    c.adresse_livraison = u.adresse,
    c.ville_livraison = u.ville,
    c.code_postal_livraison = CAST(u.code_postal AS CHAR),
    c.heure_livraison = CASE c.id_commande
        WHEN 1 THEN '10:15:00'
        WHEN 2 THEN '11:30:00'
        WHEN 3 THEN '14:00:00'
        WHEN 4 THEN '12:45:00'
        WHEN 5 THEN '15:30:00'
        WHEN 6 THEN '10:45:00'
        WHEN 7 THEN '13:15:00'
        WHEN 8 THEN '11:00:00'
        WHEN 9 THEN '14:30:00'
        WHEN 10 THEN '15:45:00'
    END
WHERE c.id_commande BETWEEN 1 AND 10;


-- Étape 3 : Adapter les quantités des commandes de test

UPDATE commandes
SET qte_personnes = CASE id_commande
    WHEN 1 THEN 12
    WHEN 3 THEN 16
    WHEN 5 THEN 15
    WHEN 9 THEN 11
    ELSE qte_personnes
END
WHERE id_commande BETWEEN 1 AND 10;


-- Étape 4 : Recalculer les montants à partir des tarifs des menus

UPDATE commandes c
INNER JOIN menus m
    ON c.menu = m.id_menu
SET
    c.prix_menus = ROUND(m.prix_personne * c.qte_personnes, 2),

    c.reduction_groupe = CASE
        WHEN c.qte_personnes >= m.min_personnes + 5
        THEN ROUND(m.prix_personne * c.qte_personnes * 0.10, 2)
        ELSE 0.00
    END,

    c.prix_livraison = 5.00,
    c.frais_kilometriques = 0.00,

    c.prix_total = ROUND(
        m.prix_personne * c.qte_personnes
        -
        CASE
            WHEN c.qte_personnes >= m.min_personnes + 5
            THEN ROUND(m.prix_personne * c.qte_personnes * 0.10, 2)
            ELSE 0.00
        END
        + 5.00,
        2
    )
WHERE c.id_commande BETWEEN 1 AND 10;


-- Étape 5 : Mettre à jour le matériel et les statuts

UPDATE commandes
SET
    pret_materiel = CASE
        WHEN id_commande IN (2, 4, 6, 8) THEN FALSE
        ELSE TRUE
    END,

    restitution = CASE
        WHEN id_commande IN (2, 4, 6, 8, 10) THEN FALSE
        ELSE TRUE
    END,

    statut = CASE
        WHEN id_commande = 10 THEN 'En cours'
        ELSE 'Clôturé'
    END
WHERE id_commande BETWEEN 1 AND 10;


-- Étape 6 : Finaliser les types et les contraintes

ALTER TABLE commandes
    MODIFY COLUMN heure_livraison TIME NOT NULL,
    MODIFY COLUMN adresse_livraison VARCHAR(255) NOT NULL,
    MODIFY COLUMN ville_livraison VARCHAR(100) NOT NULL,
    MODIFY COLUMN code_postal_livraison VARCHAR(20) NOT NULL,
    MODIFY COLUMN prix_total DECIMAL(10,2) NOT NULL,
    MODIFY COLUMN pret_materiel BOOLEAN NOT NULL DEFAULT FALSE,
    MODIFY COLUMN restitution BOOLEAN NOT NULL DEFAULT FALSE,

    ADD CONSTRAINT chk_pret_materiel
        CHECK (pret_materiel IN (0, 1)),

    ADD CONSTRAINT chk_restitution
        CHECK (restitution IN (0, 1)),

    ADD CONSTRAINT chk_restitution_coherente
        CHECK (restitution = FALSE OR pret_materiel = TRUE);


-- Corriger la date de création de la commande n°8

UPDATE commandes
SET date_create = '2026-05-10'
WHERE id_commande = 8;