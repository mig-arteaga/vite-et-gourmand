SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Migration 004 : Fonctionnalités de l'espace administrateur (activation/désactivation des comptes employés)

ALTER TABLE utilisateurs
    ADD COLUMN actif BOOLEAN NOT NULL DEFAULT TRUE AFTER role,
    ADD CONSTRAINT chk_utilisateurs_actif CHECK (actif IN (0, 1));

