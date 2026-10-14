-- Migration 002 : protection contre les commandes en double

-- 1. Ajouter temporairement la colonne nullable
ALTER TABLE commandes
ADD COLUMN order_token CHAR(32) NULL;

-- 2. Générer un jeton unique pour chaque commande existante
UPDATE commandes
SET order_token = REPLACE(UUID(), '-', '')
WHERE order_token IS NULL;

-- 3. Rendre la colonne obligatoire et unique
ALTER TABLE commandes
MODIFY COLUMN order_token CHAR(32) NOT NULL,
ADD CONSTRAINT uq_commandes_order_token UNIQUE (order_token);