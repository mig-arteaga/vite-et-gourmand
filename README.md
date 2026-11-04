# Vite & Gourmand

Application web de gestion de menus sur mesure, de suivi de commandes et d'analyse statistique développée pour l'entreprise de restauration et traiteur **Vite & Gourmand** (Julie & José) dans le cadre du Titre Professionnel **Développeur Web et Web Mobile (DWWM — Niveau 5)**.

---

## Liens du projet

- **Application déployée (Heroku)** : https://vite-et-gourmand-maao-64b0a66b9b27.herokuapp.com/
- **Dépôt GitHub** : https://github.com/mig-arteaga/vite-et-gourmand
- **Gestion de projet (Jira)** : https://miguel-arteaga.atlassian.net/jira/software/projects/VG/boards/2/timeline?atlOrigin=eyJpIjoiOWQ4MTJmMTEyZjM0NGJmNmExYmM1MzFiOTNlNzY0NTAiLCJwIjoiaiJ9
- **Documentation & Livrables ECF (`documentation/`)** :
  - `01_Manuel_utilisation.pdf`
  - `02_Charte_graphique.pdf`
  - `03_Gestion_de_projet.pdf`
  - `04_Documentation_technique.pdf`
  - `05_Diagrammes_UML_et_Donnees.md` (Diagrammes UML de cas d'utilisation, classes, séquence, MCD/MLD/MPD MySQL et schéma documentaire NoSQL MongoDB)

---

## Architecture & Technologies utilisées

### Front-End
- **HTML5 / CSS3** (Responsive design Mobile & Desktop, conformité accessibilité **RGAA**)
- **JavaScript ES6 (Modules natifs)** : filtrage dynamique des menus sans rechargement, modales accessibles, calcul en temps réel des frais kilométriques et du tarif de commande.

### Back-End & Bases de données
- **PHP 8.2** : Architecture **MVC** et **Programmation Orientée Objet (POO)** avec typage strict (`declare(strict_types=1)`), autoloading PSR-4 via Composer, routeur personnalisé (`App\Core\Router`), gestion des sessions par rôles (`App\Core\Session`) et protection anti-CSRF (`App\Core\Csrf`).
- **Base de données relationnelle — MySQL 8.0 (PDO)** :
  - Stockage transactionnel des utilisateurs, rôles, menus, plats, allergènes, horaires, commandes, historique de suivi (`commande_suivi`) et avis clients.
  - Requêtes préparées PDO exclusives et principe de moindre privilège (`docker/mysql/02-permissions.sh`).
- **Base de données NoSQL — MongoDB 7.0 (`ext-mongodb`)** :
  - Stockage documentaire des commandes par menu dans la collection `vite_et_gourmand_stats.orders_stats` (`App\Repositories\StatsNoSqlRepository`) pour alimenter le comparatif graphique et le calcul du chiffre d'affaires dans l'espace administrateur (avec persistance documentaire JSON de secours sur PaaS).
- **APIs externes** :
  - Calcul d'itinéraire et validation d'adresse via **Google Geocoding / Routes API**, avec bascule automatique sur la **Base Adresse Nationale (`api-adresse.data.gouv.fr`)** et **OSRM (`router.project-osrm.org`)**.

### Infrastructure & Déploiement
- **Docker & Docker Compose** : 3 conteneurs orchestrés (`web` PHP 8.2-Apache, `db` MySQL 8.0, `mongo` MongoDB 7.0).
- **Heroku & Railway MySQL** : Déploiement continu en production.

---

## Fonctionnalités principales par rôle

### 1. Visiteur & Authentification (`/`, `/menus`, `/nous`, `/contact`, `/register`, `/login`, `/forgot-password`)
- Consultation de la présentation de l'équipe, des avis clients validés et des horaires d'ouverture dynamiques.
- Catalogue des menus avec filtres dynamiques (fourchette de prix, nombre minimum de personnes, thème, régime alimentaire) et fiche détaillée (entrées, plats, desserts, liste des allergènes, stock et délai de commande).
- Création de compte client (`/register`) avec validation de mot de passe fort (**10 caractères min., 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial**) et e-mail de bienvenue.
- Réinitialisation sécurisée du mot de passe (`/forgot-password` & `/reset-password`) par jeton temporaire haché SHA-256 valable 1 heure.

### 2. Espace Client (`/commande` & `/compte`)
- **Passation de commande (`/commande`)** :
  - Pré-remplissage automatique des coordonnées client.
  - Calcul automatique de la distance de livraison depuis Grenoble (frais de base 5,00 € + 0,59 € / km hors Grenoble).
  - Application automatique de la **réduction de groupe de -10 %** à partir de `min_personnes + 5` convives.
  - Transaction SQL atomique avec décrémentation du stock et envoi d'un e-mail de confirmation.
- **Espace personnel (`/compte`)** :
  - Modification de ses coordonnées personnelles.
  - Suivi chronologique horodaté de l'avancement de ses commandes (`commande_suivi`).
  - **Modification** (date, heure, adresse, nombre de personnes avec recalcul du tarif et du stock, prêt de matériel — tout sauf le menu) ou **annulation** d'une commande tant qu'elle est au statut **`En attente`**.
  - **Dépôt d'un avis client** (note de 1 à 5 étoiles + commentaire, **1 seul avis par commande**) dès que la commande passe au statut **`Terminée`**.

### 3. Espace Employé (`/employe`)
- **Suivi et traitement des commandes** : filtrage par statut et par client, mise à jour du statut (`En attente` → `Accepté` → `En préparation` → `En cours de livraison` → `Livré` → `En attente du retour de matériel` → `Terminée`), notification e-mail automatique pour le retour de matériel (délai de 10 jours ouvrés / pénalité contractuelle de 600 €) et pour l'invitation à déposer un avis.
- **Annulation par un employé** : saisie obligatoire du **mode de contact** utilisé pour prévenir le client (`Appel téléphonique` ou `E-mail`) et du **motif d'annulation**, avec restitution automatique du stock.
- **Modération des avis clients** : validation ou refus des avis soumis avant publication sur la page d'accueil.
- **Gestion du catalogue (CRUD complet)** : création, modification et suppression (avec modale de confirmation) des **menus**, des **plats** et de leurs **allergènes**.
- **Gestion des horaires d'ouverture** : mise à jour des créneaux des 7 jours de la semaine affichés dans le pied de page.

### 4. Espace Administrateur (`/admin`)
- Accès complet à l'espace employé + tableau de bord administrateur.
- **Gestion des collaborateurs** : création de comptes **Employé** uniquement (impossibilité de créer un compte Administrateur), notification e-mail au collaborateur (sans transmission du mot de passe en clair) et activation/désactivation instantanée des comptes employés.
- **Statistiques & Chiffre d'affaires (NoSQL MongoDB)** : indicateurs clés (CA, commandes validées, convives servis, panier moyen), filtres par menu et par période de livraison, tableau détaillé et graphique comparatif alimentés par MongoDB.

---

## Installation et lancement en local (Docker Compose)

### 1. Prérequis
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (incluant Docker Compose)
- [Git](https://git-scm.com/)

### 2. Cloner le dépôt et configurer les variables d'environnement
```bash
git clone https://github.com/mig-arteaga/vite-et-gourmand.git
cd vite-et-gourmand
cp .env.example .env
```

### 3. Démarrer les conteneurs (PHP 8.2-Apache, MySQL 8.0, MongoDB 7.0)
```bash
docker compose up -d --build
```

Lors du premier démarrage :
1. Le conteneur `db` (MySQL 8.0) exécute automatiquement `docker/mysql/01-init.sql` (création du schéma complet en `utf8mb4` et insertion du jeu de données initial : rôles, utilisateurs, 10 menus, 15 plats, 14 allergènes, 20 commandes, historique de suivi, avis et horaires) puis `docker/mysql/02-permissions.sh` (restriction des droits de l'utilisateur applicatif aux opérations DML `SELECT`, `INSERT`, `UPDATE`, `DELETE`).
2. Le conteneur `mongo` (MongoDB 7.0) démarre la base documentaire `vite_et_gourmand_stats`.
3. Le conteneur `web` (PHP 8.2 + Apache + `pdo_mysql` + `mongodb`) expose l'application sur le port **8080**.

Ouvrez ensuite dans votre navigateur :
```text
http://localhost:8080
```

*(Note : si vous mettez à jour un volume MySQL existant, les scripts incrémentaux sont disponibles dans `docker/mysql/migrations/003_employe_features.sql` à `006_password_reset.sql`.)*

---

## Comptes de démonstration

| Rôle | Adresse e-mail | Mot de passe | Droits & Accès |
| :--- | :--- | :--- | :--- |
| **Administrateur** | `j.gourmand@mail.com` | `jose123` | Accès complet : `/admin`, `/employe`, `/compte`, `/commande` |
| **Employé** | `j.vite@mail.com` | `julie123` | Accès espace employé (`/employe`), `/compte`, `/commande` |
| **Client (Utilisateur)** | `j.peck@mail.com` | `josh123` | Passage de commande (`/commande`) et espace client (`/compte`) |
