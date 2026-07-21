-- Create database

CREATE DATABASE vite_et_gourmand

USE vite_et_gourmand

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
	role INT NOT NULL,
	FOREIGN KEY (role) REFERENCES roles(id_role)
);

CREATE TABLE avis (
	id_avis INT PRIMARY KEY AUTO_INCREMENT,
	utilisateur INT NOT NULL,
	note INT NOT NULL,
	message TEXT NOT NULL,
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
	date_create DATE NOT NULL DEFAULT NOW(),
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
roles
allergenes
themes
regimes
utilisateurs
avis
plats
plats_allergenes
menus
menus_plats
commandes