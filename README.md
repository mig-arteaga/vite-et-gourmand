# Vite & Gourmand

Application web de gestion et de commande de menus pour l'entreprise **Vite & Gourmand**.

---

## Liens du projet

### Dépôt GitHub
https://github.com/mig-arteaga/vite-et-gourmand

### Application déployée
https://vite-et-gourmand-maao-64b0a66b9b27.herokuapp.com/

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

### Création de la base
Importer le fichier SQL :
```
/sql/vite-et-gourmand.sql
```

Ce fichier contient :
* création de la base de données
* création des tables
* relations entre les tables
* données nécessaires au fonctionnement de l'application

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