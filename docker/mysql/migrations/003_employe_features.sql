SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Migration 003 : Fonctionnalités de l'espace employé et suivi des commandes

-- 1. Ajouter les colonnes d'annulation / modification par l'employé sur la table commandes
ALTER TABLE commandes
    ADD COLUMN mode_contact VARCHAR(50) NULL AFTER statut,
    ADD COLUMN motif_annulation TEXT NULL AFTER mode_contact;

-- 2. Mettre à jour les statuts des commandes existantes selon le référentiel ECF
UPDATE commandes
SET statut = 'Terminée'
WHERE statut = 'Clôturé';

UPDATE commandes
SET statut = 'En préparation'
WHERE statut = 'En cours';

-- 3. Créer la table de suivi historique des statuts de commande
CREATE TABLE IF NOT EXISTS commande_suivi (
    id_suivi INT PRIMARY KEY AUTO_INCREMENT,
    commande INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande) REFERENCES commandes(id_commande) ON DELETE CASCADE
);

-- 4. Créer la table des horaires d'ouverture
CREATE TABLE IF NOT EXISTS horaires (
    id_horaire INT PRIMARY KEY AUTO_INCREMENT,
    jour VARCHAR(20) NOT NULL,
    heure_ouverture TIME NULL,
    heure_fermeture TIME NULL,
    ferme BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_horaires_ferme
        CHECK (ferme IN (0, 1))
);

-- 5. Initialiser l'historique des 10 commandes de démonstration
INSERT INTO commande_suivi (commande, statut, date_modification) VALUES
(1, 'En attente', '2026-01-02 09:00:00'),
(1, 'Accepté', '2026-01-03 10:00:00'),
(1, 'En préparation', '2026-01-20 07:30:00'),
(1, 'En cours de livraison', '2026-01-20 09:45:00'),
(1, 'Livré', '2026-01-20 10:15:00'),
(1, 'En attente du retour de matériel', '2026-01-20 12:00:00'),
(1, 'Terminée', '2026-01-22 14:00:00'),
(2, 'En attente', '2026-01-15 11:00:00'),
(2, 'Accepté', '2026-01-16 09:30:00'),
(2, 'En préparation', '2026-02-05 08:30:00'),
(2, 'En cours de livraison', '2026-02-05 11:00:00'),
(2, 'Livré', '2026-02-05 11:30:00'),
(2, 'Terminée', '2026-02-05 11:35:00'),
(3, 'En attente', '2026-02-10 14:00:00'),
(3, 'Accepté', '2026-02-11 10:00:00'),
(3, 'En préparation', '2026-02-18 10:00:00'),
(3, 'En cours de livraison', '2026-02-18 13:30:00'),
(3, 'Livré', '2026-02-18 14:00:00'),
(3, 'En attente du retour de matériel', '2026-02-18 16:00:00'),
(3, 'Terminée', '2026-02-20 11:00:00'),
(4, 'En attente', '2026-02-18 15:00:00'),
(4, 'Accepté', '2026-02-19 09:00:00'),
(4, 'En préparation', '2026-02-26 09:30:00'),
(4, 'En cours de livraison', '2026-02-26 12:15:00'),
(4, 'Livré', '2026-02-26 12:45:00'),
(4, 'Terminée', '2026-02-26 12:50:00'),
(5, 'En attente', '2026-03-22 10:30:00'),
(5, 'Accepté', '2026-03-23 11:00:00'),
(5, 'En préparation', '2026-04-05 11:30:00'),
(5, 'En cours de livraison', '2026-04-05 15:00:00'),
(5, 'Livré', '2026-04-05 15:30:00'),
(5, 'En attente du retour de matériel', '2026-04-05 17:00:00'),
(5, 'Terminée', '2026-04-07 10:00:00'),
(6, 'En attente', '2026-04-05 16:00:00'),
(6, 'Accepté', '2026-04-06 09:00:00'),
(6, 'En préparation', '2026-04-30 07:45:00'),
(6, 'En cours de livraison', '2026-04-30 10:15:00'),
(6, 'Livré', '2026-04-30 10:45:00'),
(6, 'Terminée', '2026-04-30 10:50:00'),
(7, 'En attente', '2026-03-12 13:00:00'),
(7, 'Accepté', '2026-03-13 10:00:00'),
(7, 'En préparation', '2026-04-05 09:30:00'),
(7, 'En cours de livraison', '2026-04-05 12:45:00'),
(7, 'Livré', '2026-04-05 13:15:00'),
(7, 'En attente du retour de matériel', '2026-04-05 15:00:00'),
(7, 'Terminée', '2026-04-08 09:30:00'),
(8, 'En attente', '2026-05-10 12:00:00'),
(8, 'Accepté', '2026-05-11 08:30:00'),
(8, 'En préparation', '2026-05-15 08:00:00'),
(8, 'En cours de livraison', '2026-05-15 10:30:00'),
(8, 'Livré', '2026-05-15 11:00:00'),
(8, 'Terminée', '2026-05-15 11:05:00'),
(9, 'En attente', '2026-05-01 17:00:00'),
(9, 'Accepté', '2026-05-02 10:00:00'),
(9, 'En préparation', '2026-05-20 10:30:00'),
(9, 'En cours de livraison', '2026-05-20 14:00:00'),
(9, 'Livré', '2026-05-20 14:30:00'),
(9, 'En attente du retour de matériel', '2026-05-20 16:30:00'),
(9, 'Terminée', '2026-05-22 15:00:00'),
(10, 'En attente', '2026-07-10 11:20:00'),
(10, 'Accepté', '2026-07-11 09:15:00'),
(10, 'En préparation', '2026-08-01 11:00:00');

-- 6. Initialiser les horaires d'ouverture
INSERT INTO horaires (jour, heure_ouverture, heure_fermeture, ferme) VALUES
('Lundi', '11:30:00', '22:30:00', FALSE),
('Mardi', '11:30:00', '22:30:00', FALSE),
('Mercredi', '11:30:00', '22:30:00', FALSE),
('Jeudi', '11:30:00', '22:30:00', FALSE),
('Vendredi', '11:30:00', '22:30:00', FALSE),
('Samedi', '11:30:00', '22:30:00', FALSE),
('Dimanche', '11:30:00', '22:30:00', FALSE);

