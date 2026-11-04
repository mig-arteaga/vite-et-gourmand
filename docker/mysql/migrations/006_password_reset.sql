SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

-- Migration 006 : Réinitialisation sécurisée du mot de passe (jeton temporaire et expiration)

ALTER TABLE utilisateurs
    ADD COLUMN reset_token VARCHAR(64) NULL AFTER actif,
    ADD COLUMN reset_expires_at DATETIME NULL AFTER reset_token,
    ADD INDEX idx_utilisateurs_reset_token (reset_token);

