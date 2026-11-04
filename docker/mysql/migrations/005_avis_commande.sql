SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Migration 005 : Rattachement unique d'un avis client à une commande terminée (1 avis maximum par commande)

ALTER TABLE avis
    ADD COLUMN commande INT NULL UNIQUE AFTER utilisateur,
    ADD CONSTRAINT fk_avis_commande
        FOREIGN KEY (commande) REFERENCES commandes(id_commande) ON DELETE SET NULL;

-- Rattachement des avis initiaux à leur commande terminée respective
UPDATE avis SET commande = 2 WHERE id_avis = 1 AND commande IS NULL;
UPDATE avis SET commande = 1 WHERE id_avis = 2 AND commande IS NULL;
UPDATE avis SET commande = 3 WHERE id_avis = 3 AND commande IS NULL;
UPDATE avis SET commande = 4 WHERE id_avis = 4 AND commande IS NULL;
UPDATE avis SET commande = 5 WHERE id_avis = 5 AND commande IS NULL;

