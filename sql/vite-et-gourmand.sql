-- Create database

CREATE DATABASE vite_et_gourmand;

USE vite_et_gourmand;

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
	mot_de_passe VARCHAR(50) NOT NULL,
	telephone VARCHAR(50) NOT NULL,
	adresse VARCHAR(100) NOT NULL,
	code_postal INT NOT NULL,
	ville VARCHAR(50) NOT NULL,
    photo VARCHAR(255),
	role INT NOT NULL,
	FOREIGN KEY (role) REFERENCES roles(id_role)
);

CREATE TABLE avis (
	id_avis INT PRIMARY KEY AUTO_INCREMENT,
	utilisateur INT NOT NULL,
    date_avis DATE NOT NULL DEFAULT (CURRENT_DATE),
	note INT NOT NULL,
	message TEXT NOT NULL,
    statut VARCHAR(50) NOT NULL DEFAULT "En attente",
	FOREIGN KEY (utilisateur) REFERENCES utilisateurs(id_utilisateur)
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
	description VARCHAR(50) NOT NULL,
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
	utilisateur INT NOT NULL,
	menu INT NOT NULL,
	date_create DATE NOT NULL DEFAULT (CURRENT_DATE),
	date_livraison DATE NOT NULL,
	qte_personnes INT NOT NULL,
	prix_menus DECIMAL(10,2) NOT NULL,
	prix_livraison DECIMAL(10,2) NOT NULL,
	pret_materiel INT NOT NULL DEFAULT 0,
	restitution INT NOT NULL DEFAULT 0,
	statut VARCHAR(50) NOT NULL DEFAULT "En attente",
	FOREIGN KEY (utilisateur) REFERENCES utilisateurs(id_utilisateur),
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
('Elégant');

INSERT INTO regimes (libelle) VALUES
('Sans régime'),
('Végétarien'),
('Vegan'),
('Sans gluten');

INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, adresse, code_postal, ville, photo, role) VALUES
('Gourmand', 'José', 'j.gourmand@mail.com', 'jose123', '+33 1 23 45 67 89', '3 Place Notre Dame', 38000, 'Grenoble', 'images/profile-1.jpg', 1),
('Vite', 'Julie', 'j.vite@mail.com', 'julie123', '+33 2 23 45 67 89', 'Adresse 2', 38000, 'Grenoble', 'images/profile-2.jpg', 1),
('Admin', 'Miguel', 'm.admin@mail.com', 'miguel123', '+33 3 23 45 67 89', 'Adresse 3', 38000, 'Grenoble', 'images/profile-3.jpg', 2),
('Peck', 'Josh', 'j.peck@mail.com', 'josh123', '+33 4 23 45 67 89', 'Adresse 4', 38000, 'Grenoble', 'images/profile-4.jpg', 3),
('Ham', 'Klaudia', 'k.ham@mail.com', 'klaudia123', '+33 5 23 45 67 89', 'Adresse 5', 38000, 'Grenoble', 'images/profile-5.jpg', 3),
('Berly', 'Kim', 'k.berly@mail.com', 'kim123', '+33 6 23 45 67 89', 'Adresse 6', 38000, 'Grenoble', 'images/profile-6.jpg', 3),
('Urban', 'Karl', 'k.urban@mail.com', 'karl123', '+33 7 23 45 67 89', 'Adresse 7', 38000, 'Grenoble', 'images/profile-7.jpg', 3),
('Zeroual', 'Sif', 's.zeroual@mail.com', 'sif123', '+33 8 23 45 67 89', 'Adresse 8', 38000, 'Grenoble', 'images/profile-8.jpg', 3);

INSERT INTO avis (utilisateur, date_avis, note, message, statut) VALUES
(4, '2026-06-15', 5, 'Nous avons fait appel à Vite & Gourmand pour un repas de famille et nous avons été très satisfaits. Les plats étaient savoureux, bien présentés et préparés avec des produits de qualité. Toute l’équipe a été très professionnelle du début à la fin.', 'Validé'),
(5, '2026-05-28', 4, 'Très bonne expérience avec un service efficace et des menus variés. Les portions étaient généreuses et les invités ont beaucoup apprécié la qualité des plats proposés. Petit bémol sur le délai de livraison légèrement plus long que prévu, mais cela reste une excellente prestation.', 'Validé'),
(6, '2026-04-10', 5, 'Une prestation parfaite pour notre événement professionnel. La commande était conforme à nos attentes, les produits étaient frais et la présentation soignée. L’accompagnement de l’équipe nous a permis d’organiser notre repas en toute tranquillité.', 'Validé'),
(7, '2026-03-22', 3, 'Le repas était globalement bon et les produits semblaient de bonne qualité. Cependant, quelques ajustements pourraient être faits concernant l’organisation de la livraison et la communication avant l’événement. Malgré cela, nous avons passé un agréable moment.', 'Validé'),
(8, '2026-02-14', 4, 'Nous recommandons vivement Vite & Gourmand pour vos événements. Les menus proposés sont équilibrés, gourmands et adaptés à différents besoins. L’équipe est disponible et à l’écoute pour personnaliser la prestation selon les attentes des clients.', 'En attente');

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
('Menu Anniversaire Gourmand', 'Un menu complet pour célébrer un anniversaire', 2, 1, 8, 35.00, 5, 30),
('Menu Élégance', 'Une sélection raffinée pour vos événements', 7, 1, 6, 45.00, 7, 25),
('Menu Afterwork Convivial', 'Un menu simple et gourmand entre collègues', 3, 1, 10, 28.00, 5, 50),
('Menu Brunch Fraîcheur', 'Une formule légère et équilibrée', 4, 2, 5, 30.00, 6, 20),
('Menu Cocktail Prestige', 'Une formule idéale pour vos réceptions', 5, 1, 10, 40.00, 7, 35),
('Menu Picnic Nature', 'Une formule pratique pour vos repas extérieurs', 6, 1, 8, 32.00, 5, 40),
('Menu Végétarien Découverte', 'Une sélection sans viande pleine de saveurs', 1, 2, 6, 29.00, 6, 25),
('Menu Saveurs du Terroir', 'Des plats traditionnels et généreux', 1, 1, 10, 38.00, 7, 45),
('Menu Sans Gluten', 'Une formule adaptée aux intolérances', 7, 4, 5, 36.00, 6, 20),
('Menu Douceur Chocolatée', 'Une formule gourmande avec dessert inclus', 2, 1, 7, 34.00, 5, 30);

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

INSERT INTO commandes (utilisateur, menu, date_create, date_livraison, qte_personnes, prix_menus, prix_livraison, pret_materiel, restitution, statut) VALUES
(5, 2, '2026-01-02', '2026-01-20', 6, 270.00, 20.00, 1, 1, 'Livré'),
(4, 1, '2026-01-15', '2026-02-05', 8, 280.00, 18.00, 1, 1, 'Livré'),
(6, 3, '2026-02-10', '2026-02-18', 10, 280.00, 15.00, 1, 1, 'Livré'),
(7, 4, '2026-02-18', '2026-02-26', 5, 150.00, 10.00, 1, 1, 'Livré'),
(8, 5, '2026-03-22', '2026-04-05', 10, 400.00, 30.00, 1, 1, 'Livré'),
(4, 6, '2026-04-05', '2026-04-30', 8, 256.00, 14.00, 1, 1, 'Livré'),
(5, 7, '2026-03-12', '2026-04-05', 6, 174.00, 12.00, 1, 1, 'Livré'),
(6, 8, '2026-05-20', '2026-05-15', 10, 380.00, 25.00, 1, 1, 'Livré'),
(7, 9, '2026-05-01', '2026-05-20', 5, 180.00, 9.00, 1, 1, 'Livré'),
(8, 10, '2026-07-10', '2026-08-01', 7, 238.00, 16.00, 1, 1, 'En cours');