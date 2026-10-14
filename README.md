# Vite & Gourmand

Application web de gestion et de commande de menus pour l'entreprise **Vite & Gourmand**.

---

## Liens du projet

### Application déployée
https://vite-et-gourmand-maao-64b0a66b9b27.herokuapp.com/

### Gestion de projet 
https://miguel-arteaga.atlassian.net/jira/software/projects/VG/boards/2/timeline?atlOrigin=eyJpIjoiOWQ4MTJmMTEyZjM0NGJmNmExYmM1MzFiOTNlNzY0NTAiLCJwIjoiaiJ9

---

## Technologies utilisées
* HTML5, CSS3, JavaScript
* PHP, PDO
* MySQL
* GitHub, Heroku, Railway MySQL

---

## Installation en local

### Prérequis
Avant l'installation, il est nécessaire d'avoir :
* PHP
* MySQL
* Un serveur local PHP
* Git

---

### Cloner le projet
Dans un terminal :
```bash
git clone https://github.com/mig-arteaga/vite-et-gourmand.git
```

---

### Création de la base de données

La base de données MySQL est initialisée automatiquement
lors du premier démarrage de Docker Compose.

Le fichier utilisé est :

`docker/mysql/01-init.sql`

Ce fichier contient :
- La création des tables
- Les relations entre les tables
- Les données initiales nécessaires au fonctionnement de l'application

**Remarque :** le script d'initialisation s'exécute uniquement
lorsque le volume de données MySQL est vide.

Lors de la première initialisation de la base de données, Docker exécute automatiquement les scripts du dossier `docker/mysql/` dans l'ordre :

1. `01-init.sql` : crée les tables et insère les données initiales.
2. `02-permissions.sh` : limite les privilèges de l'utilisateur MySQL applicatif aux opérations `SELECT`, `INSERT`, `UPDATE` et `DELETE`.

Ces scripts ne sont exécutés automatiquement que lorsque le volume MySQL est vide.

---

### Configuration de la connexion MySQL

Ouvrir le fichier :
```
config/database.php
```

et modifier avec les paramètres locaux :
```php
$host = "localhost";
$dbname = "vite_et_gourmand";
$username = "root";
$password = "";
```

avec les valeurs de votre installation MySQL.

---

### Lancement de l'application en local

Depuis le dossier du projet :
```bash
php -S localhost:8000
```

Puis ouvrir dans un navigateur :
```
http://localhost:8000
```

---

## Comptes de démonstration

### Administrateur
Email :
```
j.gourmand@mail.com
```

Mot de passe :
```
jose123
```

Droits :
* accès à l'espace employé
* accès à l'espace administrateur

---

### Employé
Email :
```
j.vite@mail.com
```

Mot de passe :
```
julie123
```

Droits :
* accès à l'espace employé

---

### Utilisateur
Email :
```
j.peck@mail.com
```

Mot de passe :
```
josh123
```

Droits :
* consultation des menus
* accès à l'espace mon compte