SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE vite_et_gourmand;

-- Drop existing tables in reverse foreign-key order (for clean re-initialization)
DROP TABLE IF EXISTS photos_menu;
DROP TABLE IF EXISTS horaires;
DROP TABLE IF EXISTS commande_suivi;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS commandes;
DROP TABLE IF EXISTS menus_plats;
DROP TABLE IF EXISTS menus;
DROP TABLE IF EXISTS plats_allergenes;
DROP TABLE IF EXISTS plats;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS regimes;
DROP TABLE IF EXISTS themes;
DROP TABLE IF EXISTS allergenes;
DROP TABLE IF EXISTS roles;

-- Create tables

CREATE TABLE roles (
	id_role INT PRIMARY KEY AUTO_INCREMENT,
	libelle VARCHAR(100) NOT NULL
);

CREATE TABLE allergenes (
	id_allergene INT PRIMARY KEY AUTO_INCREMENT,
	 libelle VARCHAR(100) NOT NULL
);

CREATE TABLE themes (
	id_theme INT PRIMARY KEY AUTO_INCREMENT,
	 libelle VARCHAR(100) NOT NULL
);

CREATE TABLE regimes (
	id_regime INT PRIMARY KEY AUTO_INCREMENT,
	 libelle VARCHAR(100) NOT NULL
);

CREATE TABLE utilisateurs (
	id_utilisateur INT PRIMARY KEY AUTO_INCREMENT,
	nom VARCHAR(50) NOT NULL,
	prenom VARCHAR(50) NOT NULL,
	email VARCHAR(100) NOT NULL,
	mot_de_passe VARCHAR(255) NOT NULL,
	telephone VARCHAR(50) NOT NULL,
	adresse VARCHAR(100) NOT NULL,
	code_postal INT NOT NULL,
	ville VARCHAR(50) NOT NULL,
    photo VARCHAR(255),
	role INT NOT NULL,
    actif BOOLEAN NOT NULL DEFAULT TRUE,
    reset_token VARCHAR(64) NULL,
    reset_expires_at DATETIME NULL,
    CONSTRAINT chk_utilisateurs_actif
        CHECK (actif IN (0, 1)),
    INDEX idx_utilisateurs_reset_token (reset_token),
	FOREIGN KEY (role) REFERENCES roles(id_role)
);

CREATE TABLE plats (
	id_plat INT PRIMARY KEY AUTO_INCREMENT,
	titre VARCHAR(50) NOT NULL,
	description VARCHAR(50) NOT NULL,
	type VARCHAR(100) NOT NULL
);

CREATE TABLE plats_allergenes (
	plat INT NOT NULL,
	allergene INT NOT NULL,
	FOREIGN KEY (plat) REFERENCES plats(id_plat),
	FOREIGN KEY (allergene) REFERENCES allergenes(id_allergene),
	PRIMARY KEY (plat, allergene)
);

CREATE TABLE menus (
	id_menu INT PRIMARY KEY AUTO_INCREMENT,
	titre VARCHAR(50) NOT NULL,
	description VARCHAR(255) NOT NULL,
	theme INT NOT NULL,
	regime INT NOT NULL,
	min_personnes INT NOT NULL DEFAULT 5,
	prix_personne DECIMAL(10,2) NOT NULL,
	jours_avant INT NOT NULL DEFAULT 3,
	stock INT NOT NULL DEFAULT 0,
	FOREIGN KEY (theme) REFERENCES themes(id_theme),
	FOREIGN KEY (regime) REFERENCES regimes(id_regime)
);

CREATE TABLE menus_plats (
	menu INT NOT NULL,
	plat INT NOT NULL,
	FOREIGN KEY (menu) REFERENCES menus(id_menu),
	FOREIGN KEY (plat) REFERENCES plats(id_plat),
	PRIMARY KEY (menu, plat)
);

CREATE TABLE commandes (
    id_commande INT PRIMARY KEY AUTO_INCREMENT,
	order_token CHAR(32) NOT NULL UNIQUE,
    utilisateur INT NOT NULL,
    menu INT NOT NULL,
    date_create DATE NOT NULL DEFAULT (CURRENT_DATE),
    date_livraison DATE NOT NULL,
    heure_livraison TIME NOT NULL,
    adresse_livraison VARCHAR(255) NOT NULL,
    ville_livraison VARCHAR(100) NOT NULL,
    code_postal_livraison VARCHAR(20) NOT NULL,
    qte_personnes INT NOT NULL,
    prix_menus DECIMAL(10,2) NOT NULL,
    prix_livraison DECIMAL(10,2) NOT NULL,
	reduction_groupe DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	frais_kilometriques DECIMAL(10,2) NOT NULL DEFAULT 0.00,
	prix_total DECIMAL(10,2) NOT NULL,
    pret_materiel BOOLEAN NOT NULL DEFAULT FALSE,
    restitution BOOLEAN NOT NULL DEFAULT FALSE,
    statut VARCHAR(50) NOT NULL DEFAULT 'En attente',
    mode_contact VARCHAR(50) NULL,
    motif_annulation TEXT NULL,
    CONSTRAINT chk_pret_materiel
        CHECK (pret_materiel IN (0, 1)),
    CONSTRAINT chk_restitution
        CHECK (restitution IN (0, 1)),
    CONSTRAINT chk_restitution_coherente
        CHECK (restitution = FALSE OR pret_materiel = TRUE),
    FOREIGN KEY (utilisateur) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (menu) REFERENCES menus(id_menu)
);

CREATE TABLE avis (
	id_avis INT PRIMARY KEY AUTO_INCREMENT,
	utilisateur INT NOT NULL,
    commande INT NULL UNIQUE,
    date_avis DATE NOT NULL DEFAULT (CURRENT_DATE),
	note INT NOT NULL,
	message TEXT NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT "En attente",
	FOREIGN KEY (utilisateur) REFERENCES utilisateurs(id_utilisateur),
    FOREIGN KEY (commande) REFERENCES commandes(id_commande) ON DELETE SET NULL
);

CREATE TABLE commande_suivi (
    id_suivi INT PRIMARY KEY AUTO_INCREMENT,
    commande INT NOT NULL,
    statut VARCHAR(50) NOT NULL,
    date_modification DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (commande) REFERENCES commandes(id_commande) ON DELETE CASCADE
);

CREATE TABLE horaires (
    id_horaire INT PRIMARY KEY AUTO_INCREMENT,
    jour VARCHAR(20) NOT NULL,
    heure_ouverture TIME NULL,
    heure_fermeture TIME NULL,
    ferme BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT chk_horaires_ferme
        CHECK (ferme IN (0, 1))
);

CREATE TABLE photos_menu (
    id_photo INT PRIMARY KEY AUTO_INCREMENT,
    menu INT NOT NULL,
    chemin VARCHAR(255) NOT NULL,
    ordre INT NOT NULL DEFAULT 1,
    FOREIGN KEY (menu) REFERENCES menus(id_menu)
);

-- Initialize data

INSERT INTO roles (libelle) VALUES
('Administrateur'),
('Employé'),
('Utilisateur');

INSERT INTO allergenes (libelle) VALUES
('Céréales'),
('Crustacés'),
('Œufs'),
('Poisson'),
('Arachides'),
('Soja'),
('Lait'),
('Noix'),
('Céleri'),
('Moutarde'),
('Graines'),
('Sulfites'),
('Lupin'),
('Mollusques');

INSERT INTO themes (libelle) VALUES
('Sans thème'),
('Anniversaire'),
('Afterwork'),
('Brunch'),
('Cocktail party'),
('Picnic'),
('Élégant');

INSERT INTO regimes (libelle) VALUES
('Sans régime'),
('Végétarien'),
('Vegan'),
('Sans gluten');

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville, photo, role) VALUES
('Gourmand', 'José', 'j.gourmand@mail.com', '$2y$10$DOkweWyvQXTu38qgL7yokeRa0uuritZMwoXjuytbrdV6NX/4eGynu', '+33 1 23 45 67 89', '3 Place Notre Dame', 38000, 'Grenoble', 'images/profile-1.jpg', 1),
('Admin', 'Miguel', 'm.admin@mail.com', '$2y$10$b6naitHuE/ltEPCU.VmTcebi53WYSmKNII.JKy0S6z7/Au8jmFLFO', '+33 2 23 45 67 89', 'Adresse 2', 38000, 'Grenoble', 'images/profile-2.jpg', 1),
('Vite', 'Julie', 'j.vite@mail.com', '$2y$10$SQm/7P7xTSyWXadzHJ/VzOKVWKD9LrSf991b0D6AfH3.1oJ.OLyZm', '+33 3 23 45 67 89', 'Adresse 3', 38000, 'Grenoble', 'images/profile-3.jpg', 2),
('Peck', 'Josh', 'j.peck@mail.com', '$2y$10$wPv.L1zZZP7iPkqqdcjXqukAaxSygYVDKfC8wVQaiWijeKHSl257O', '+33 4 23 45 67 89', 'Adresse 4', 38000, 'Grenoble', 'images/profile-4.jpg', 3),
('Ham', 'Klaudia', 'k.ham@mail.com', '$2y$10$UCmeosMR0opHh84N5KYnHOx6iWBjiA0XsEXO/dFRap1DEPrV1BN56', '+33 5 23 45 67 89', 'Adresse 5', 38000, 'Grenoble', 'images/profile-5.jpg', 3),
('Berly', 'Kim', 'k.berly@mail.com', '$2y$10$x4UafOSeJCZQNdsS7m.Bnesaq216GnVuLTJIsIsj9ZKgKRhju46U2', '+33 6 23 45 67 89', 'Adresse 6', 38000, 'Grenoble', 'images/profile-6.jpg', 3),
('Urban', 'Karl', 'k.urban@mail.com', '$2y$10$tSoompCjAVD4CMHBiyKokOmcaGzqXD9hnFjQwPiEKZ1zaKrL65.a6', '+33 7 23 45 67 89', 'Adresse 7', 38000, 'Grenoble', 'images/profile-7.jpg', 3),
('Zeroual', 'Sif', 's.zeroual@mail.com', '$2y$10$tXAKsjYHGCY8Gthcfd5MwOrAZ2WBTDxI/dkL8d2kyVOIPC4S89T.6', '+33 8 23 45 67 89', 'Adresse 8', 38000, 'Grenoble', 'images/profile-8.jpg', 3);

INSERT INTO plats (titre, description, type) VALUES
-- Entrées
('Salade de chèvre chaud', 'Salade fraîche avec fromage de chèvre et noix', 'Entrée'),
('Saumon fumé', 'Tranches de saumon fumé avec citron et herbes', 'Entrée'),
('Velouté de légumes', 'Soupe maison de légumes de saison', 'Entrée'),
('Terrine de campagne', 'Terrine traditionnelle accompagnée de pain', 'Entrée'),
('Crevettes marinées', 'Crevettes marinées aux agrumes et épices', 'Entrée'),
-- Plats
('Poulet rôti aux herbes', 'Poulet accompagné de légumes de saison', 'Plat'),
('Filet de saumon', 'Saumon frais avec sauce citronnée', 'Plat'),
('Risotto aux champignons', 'Risotto crémeux aux champignons frais', 'Plat'),
('Boeuf mijoté', 'Boeuf fondant avec pommes de terre', 'Plat'),
('Pâtes aux légumes', 'Pâtes fraîches aux légumes grillés', 'Plat'),
-- Desserts
('Tarte aux pommes', 'Tarte maison aux pommes caramélisées', 'Dessert'),
('Fondant au chocolat', 'Gâteau fondant au chocolat noir', 'Dessert'),
('Crème brûlée', 'Crème vanillée avec caramel croquant', 'Dessert'),
('Salade de fruits frais', 'Assortiment de fruits de saison', 'Dessert'),
('Mousse au chocolat', 'Mousse légère au chocolat noir', 'Dessert');

INSERT INTO plats_allergenes (plat, allergene) VALUES
(1, 7),
(1, 8),
(2, 4),
(3, 7),
(3, 9),
(4, 3),
(4, 10),
(5, 2),
(5, 12),
(6, 10),
(6, 9),
(7, 4),
(7, 7),
(8, 7),
(8, 3),
(9, 9),
(9, 12),
(10, 1),
(10, 6),
(11, 1),
(11, 7),
(11, 3),
(12, 7),
(12, 3),
(13, 7),
(13, 3),
(14, 12),
(15, 7),
(15, 3);

INSERT INTO menus (titre, description, theme, regime, min_personnes, prix_personne, jours_avant, stock) VALUES
('Anniversaire Gourmand', 'Une composition généreuse et festive pensée pour accompagner vos moments de célébration, avec des recettes gourmandes et raffinées qui raviront tous vos invités.', 2, 1, 8, 35.00, 5, 30),
('Élégance', 'Une sélection raffinée de mets soigneusement préparés pour vos événements prestigieux, alliant finesse, équilibre des saveurs et présentation élégante.', 7, 1, 6, 45.00, 7, 25),
('Team-building Convivial', 'Une formule chaleureuse et accessible idéale pour partager un moment agréable entre collègues autour de plats savoureux et d une ambiance conviviale.', 3, 1, 10, 28.00, 5, 50),
('Fraîcheur de Midi', 'Une formule légère et équilibrée composée de saveurs fraîches et variées, parfaite pour profiter d un brunch gourmand lors de vos rencontres.', 4, 2, 5, 30.00, 6, 20),
('Cocktail Prestige', 'Une expérience culinaire élégante conçue pour vos réceptions, avec une association de bouchées délicates et de créations originales adaptées aux grandes occasions.', 5, 1, 10, 40.00, 7, 35),
('Picnic Nature', 'Une formule pratique et savoureuse spécialement imaginée pour vos repas en extérieur, combinant simplicité, fraîcheur et plaisir autour de produits sélectionnés.', 6, 1, 8, 32.00, 5, 40),
('Végétarien Découverte', 'Une sélection créative de recettes végétariennes riches en goûts et en couleurs, mettant en valeur des ingrédients frais et des associations gourmandes.', 1, 2, 6, 29.00, 6, 25),
('Saveurs du Terroir', 'Un voyage culinaire au cœur des traditions françaises avec des plats généreux inspirés du terroir et préparés avec des produits de qualité.', 1, 1, 10, 38.00, 7, 45),
('Sans Gluten', 'Une formule adaptée aux besoins spécifiques proposant des recettes savoureuses sans gluten, tout en conservant la richesse des saveurs et le plaisir du repas.', 7, 4, 5, 36.00, 6, 20),
('Douceur Chocolatée', 'Une formule gourmande dédiée aux amateurs de douceurs, associant des recettes généreuses et des desserts chocolatés pour terminer le repas avec plaisir.', 2, 1, 7, 34.00, 5, 30);

INSERT INTO menus_plats (menu, plat) VALUES
(1, 1),
(1, 6),
(1, 11),
(2, 2),
(2, 7),
(2, 13),
(3, 4),
(3, 9),
(4, 3),
(4, 8),
(5, 5),
(5, 6),
(5, 15),
(6, 1),
(6, 10),
(6, 14),
(7, 3),
(7, 8),
(7, 14),
(8, 4),
(8, 9),
(8, 12),
(9, 2),
(9, 7),
(10, 6),
(10, 12);

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
) VALUES
(REPLACE(UUID(), '-', ''), 5, 2, '2026-01-02', '2026-01-20', '10:15:00', 'Adresse 5', 'Grenoble', '38000', 12, 540.00, 5.00, 54.00, 0.00, 491.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 4, 1, '2026-01-15', '2026-02-05', '11:30:00', 'Adresse 4', 'Grenoble', '38000', 8, 280.00, 5.00, 0.00, 0.00, 285.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 6, 3, '2026-02-10', '2026-02-18', '14:00:00', 'Adresse 6', 'Grenoble', '38000', 16, 448.00, 5.00, 44.80, 0.00, 408.20, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 7, 4, '2026-02-18', '2026-02-26', '12:45:00', 'Adresse 7', 'Grenoble', '38000', 5, 150.00, 5.00, 0.00, 0.00, 155.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 8, 5, '2026-03-22', '2026-04-05', '15:30:00', 'Adresse 8', 'Grenoble', '38000', 15, 600.00, 5.00, 60.00, 0.00, 545.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 4, 6, '2026-04-05', '2026-04-30', '10:45:00', 'Adresse 4', 'Grenoble', '38000', 8, 256.00, 5.00, 0.00, 0.00, 261.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 5, 7, '2026-03-12', '2026-04-05', '13:15:00', 'Adresse 5', 'Grenoble', '38000', 6, 174.00, 5.00, 0.00, 0.00, 179.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 6, 8, '2026-05-10', '2026-05-15', '11:00:00', 'Adresse 6', 'Grenoble', '38000', 10, 380.00, 5.00, 0.00, 0.00, 385.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 7, 9, '2026-05-01', '2026-05-20', '14:30:00', 'Adresse 7', 'Grenoble', '38000', 11, 396.00, 5.00, 39.60, 0.00, 361.40, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 8, 10, '2026-07-10', '2026-08-01', '15:45:00', 'Adresse 8', 'Grenoble', '38000', 7, 238.00, 5.00, 0.00, 0.00, 243.00, TRUE, FALSE, 'En préparation'),
(REPLACE(UUID(), '-', ''), 4, 2, '2026-01-25', '2026-02-12', '12:00:00', 'Adresse 4', 'Grenoble', '38000', 10, 450.00, 5.00, 0.00, 0.00, 455.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 5, 2, '2026-03-01', '2026-03-18', '19:30:00', 'Adresse 5', 'Grenoble', '38000', 14, 630.00, 5.00, 63.00, 0.00, 572.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 6, 2, '2026-05-12', '2026-05-28', '13:00:00', 'Adresse 6', 'Grenoble', '38000', 8, 360.00, 5.00, 0.00, 0.00, 365.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 7, 5, '2026-02-02', '2026-02-14', '20:00:00', 'Adresse 7', 'Grenoble', '38000', 12, 480.00, 5.00, 0.00, 0.00, 485.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 8, 5, '2026-04-10', '2026-04-24', '18:30:00', 'Adresse 8', 'Grenoble', '38000', 20, 800.00, 5.00, 80.00, 0.00, 725.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 4, 1, '2026-03-05', '2026-03-20', '12:30:00', 'Adresse 4', 'Grenoble', '38000', 15, 525.00, 5.00, 52.50, 0.00, 477.50, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 6, 1, '2026-06-01', '2026-06-14', '13:15:00', 'Adresse 6', 'Grenoble', '38000', 10, 350.00, 5.00, 0.00, 0.00, 355.00, FALSE, FALSE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 5, 3, '2026-04-18', '2026-05-04', '12:15:00', 'Adresse 5', 'Grenoble', '38000', 20, 560.00, 5.00, 56.00, 0.00, 509.00, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 7, 8, '2026-06-05', '2026-06-19', '19:00:00', 'Adresse 7', 'Grenoble', '38000', 16, 608.00, 5.00, 60.80, 0.00, 552.20, TRUE, TRUE, 'Terminée'),
(REPLACE(UUID(), '-', ''), 8, 6, '2026-06-10', '2026-06-25', '12:00:00', 'Adresse 8', 'Grenoble', '38000', 12, 384.00, 5.00, 0.00, 0.00, 389.00, FALSE, FALSE, 'Terminée');

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
(10, 'En préparation', '2026-08-01 11:00:00'),
(11, 'En attente', '2026-01-25 10:00:00'),
(11, 'Accepté', '2026-01-26 09:00:00'),
(11, 'En préparation', '2026-02-12 08:30:00'),
(11, 'En cours de livraison', '2026-02-12 11:15:00'),
(11, 'Livré', '2026-02-12 12:00:00'),
(11, 'Terminée', '2026-02-12 12:05:00'),
(12, 'En attente', '2026-03-01 14:00:00'),
(12, 'Accepté', '2026-03-02 10:00:00'),
(12, 'En préparation', '2026-03-18 15:00:00'),
(12, 'En cours de livraison', '2026-03-18 18:45:00'),
(12, 'Livré', '2026-03-18 19:30:00'),
(12, 'En attente du retour de matériel', '2026-03-18 21:00:00'),
(12, 'Terminée', '2026-03-20 11:00:00'),
(13, 'En attente', '2026-05-12 11:00:00'),
(13, 'Accepté', '2026-05-13 09:30:00'),
(13, 'En préparation', '2026-05-28 09:00:00'),
(13, 'En cours de livraison', '2026-05-28 12:15:00'),
(13, 'Livré', '2026-05-28 13:00:00'),
(13, 'Terminée', '2026-05-28 13:05:00'),
(14, 'En attente', '2026-02-02 16:00:00'),
(14, 'Accepté', '2026-02-03 10:00:00'),
(14, 'En préparation', '2026-02-14 16:00:00'),
(14, 'En cours de livraison', '2026-02-14 19:15:00'),
(14, 'Livré', '2026-02-14 20:00:00'),
(14, 'Terminée', '2026-02-14 20:05:00'),
(15, 'En attente', '2026-04-10 10:30:00'),
(15, 'Accepté', '2026-04-11 09:00:00'),
(15, 'En préparation', '2026-04-24 14:30:00'),
(15, 'En cours de livraison', '2026-04-24 17:45:00'),
(15, 'Livré', '2026-04-24 18:30:00'),
(15, 'En attente du retour de matériel', '2026-04-24 20:00:00'),
(15, 'Terminée', '2026-04-27 10:30:00'),
(16, 'En attente', '2026-03-05 13:00:00'),
(16, 'Accepté', '2026-03-06 09:30:00'),
(16, 'En préparation', '2026-03-20 08:30:00'),
(16, 'En cours de livraison', '2026-03-20 11:45:00'),
(16, 'Livré', '2026-03-20 12:30:00'),
(16, 'En attente du retour de matériel', '2026-03-20 14:00:00'),
(16, 'Terminée', '2026-03-23 10:00:00'),
(17, 'En attente', '2026-06-01 11:30:00'),
(17, 'Accepté', '2026-06-02 09:00:00'),
(17, 'En préparation', '2026-06-14 09:30:00'),
(17, 'En cours de livraison', '2026-06-14 12:30:00'),
(17, 'Livré', '2026-06-14 13:15:00'),
(17, 'Terminée', '2026-06-14 13:20:00'),
(18, 'En attente', '2026-04-18 15:00:00'),
(18, 'Accepté', '2026-04-19 10:00:00'),
(18, 'En préparation', '2026-05-04 08:30:00'),
(18, 'En cours de livraison', '2026-05-04 11:30:00'),
(18, 'Livré', '2026-05-04 12:15:00'),
(18, 'En attente du retour de matériel', '2026-05-04 14:00:00'),
(18, 'Terminée', '2026-05-06 11:00:00'),
(19, 'En attente', '2026-06-05 17:00:00'),
(19, 'Accepté', '2026-06-06 10:00:00'),
(19, 'En préparation', '2026-06-19 15:00:00'),
(19, 'En cours de livraison', '2026-06-19 18:15:00'),
(19, 'Livré', '2026-06-19 19:00:00'),
(19, 'En attente du retour de matériel', '2026-06-19 21:00:00'),
(19, 'Terminée', '2026-06-22 14:00:00'),
(20, 'En attente', '2026-06-10 12:00:00'),
(20, 'Accepté', '2026-06-11 09:30:00'),
(20, 'En préparation', '2026-06-25 08:30:00'),
(20, 'En cours de livraison', '2026-06-25 11:15:00'),
(20, 'Livré', '2026-06-25 12:00:00'),
(20, 'Terminée', '2026-06-25 12:05:00');

INSERT INTO avis (utilisateur, commande, date_avis, note, message, statut) VALUES
(4, 2, '2026-06-15', 5, 'Nous avons fait appel à Vite & Gourmand pour un repas de famille et nous avons été très satisfaits. Les plats étaient savoureux, bien présentés et préparés avec des produits de qualité. Toute l’équipe a été très professionnelle du début à la fin.', 'Validé'),
(5, 1, '2026-05-28', 4, 'Très bonne expérience avec un service efficace et des menus variés. Les portions étaient généreuses et les invités ont beaucoup apprécié la qualité des plats proposés. Petit bémol sur le délai de livraison légèrement plus long que prévu, mais cela reste une excellente prestation.', 'Validé'),
(6, 3, '2026-04-10', 5, 'Une prestation parfaite pour notre événement professionnel. La commande était conforme à nos attentes, les produits étaient frais et la présentation soignée. L’accompagnement de l’équipe nous a permis d’organiser notre repas en toute tranquillité.', 'Validé'),
(7, 4, '2026-03-22', 3, 'Le repas était globalement bon et les produits semblaient de bonne qualité. Cependant, quelques ajustements pourraient être faits concernant l’organisation de la livraison et la communication avant l’événement. Malgré cela, nous avons passé un agréable moment.', 'Validé'),
(8, 5, '2026-02-14', 4, 'Nous recommandons vivement Vite & Gourmand pour vos événements. Les menus proposés sont équilibrés, gourmands et adaptés à différents besoins. L’équipe est disponible et à l’écoute pour personnaliser la prestation selon les attentes des clients.', 'En attente');

INSERT INTO horaires (jour, heure_ouverture, heure_fermeture, ferme) VALUES
('Lundi', '08:30:00', '22:30:00', FALSE),
('Mardi', '08:30:00', '22:30:00', FALSE),
('Mercredi', '08:30:00', '22:30:00', FALSE),
('Jeudi', '08:30:00', '22:30:00', FALSE),
('Vendredi', '08:30:00', '22:30:00', FALSE),
('Samedi', '08:30:00', '22:30:00', FALSE),
('Dimanche', '08:30:00', '22:30:00', FALSE);

INSERT INTO photos_menu (menu, chemin, ordre) VALUES
(1,'images/menu-1-1.jpg',1),
(1,'images/menu-1-2.jpg',2),
(1,'images/menu-1-3.jpg',3),
(1,'images/menu-1-4.jpg',4),
(1,'images/menu-1-5.jpg',5),
(2,'images/menu-1-2.jpg',1),
(2,'images/menu-1-3.jpg',2),
(2,'images/menu-1-4.jpg',3),
(2,'images/menu-1-5.jpg',4),
(2,'images/menu-1-6.jpg',5),
(3,'images/menu-1-3.jpg',1),
(3,'images/menu-1-4.jpg',2),
(3,'images/menu-1-5.jpg',3),
(3,'images/menu-1-6.jpg',4),
(3,'images/menu-1-7.jpg',5),
(4,'images/menu-1-4.jpg',1),
(4,'images/menu-1-5.jpg',2),
(4,'images/menu-1-6.jpg',3),
(4,'images/menu-1-7.jpg',4),
(4,'images/menu-1-8.jpg',5),
(5,'images/menu-1-5.jpg',1),
(5,'images/menu-1-6.jpg',2),
(5,'images/menu-1-7.jpg',3),
(5,'images/menu-1-8.jpg',4),
(5,'images/menu-1-9.jpg',5),
(6,'images/menu-1-6.jpg',1),
(6,'images/menu-1-7.jpg',2),
(6,'images/menu-1-8.jpg',3),
(6,'images/menu-1-9.jpg',4),
(6,'images/menu-1-10.jpg',5),
(7,'images/menu-1-7.jpg',1),
(7,'images/menu-1-8.jpg',2),
(7,'images/menu-1-9.jpg',3),
(7,'images/menu-1-10.jpg',4),
(7,'images/menu-1-1.jpg',5),
(8,'images/menu-1-8.jpg',1),
(8,'images/menu-1-9.jpg',2),
(8,'images/menu-1-10.jpg',3),
(8,'images/menu-1-1.jpg',4),
(8,'images/menu-1-2.jpg',5),
(9,'images/menu-1-9.jpg',1),
(9,'images/menu-1-10.jpg',2),
(9,'images/menu-1-1.jpg',3),
(9,'images/menu-1-2.jpg',4),
(9,'images/menu-1-3.jpg',5),
(10,'images/menu-1-10.jpg',1),
(10,'images/menu-1-1.jpg',2),
(10,'images/menu-1-2.jpg',3),
(10,'images/menu-1-3.jpg',4),
(10,'images/menu-1-4.jpg',5);