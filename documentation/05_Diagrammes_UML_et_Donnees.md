# Diagrammes UML & Modélisation des Données (Merise & NoSQL) — Vite & Gourmand

Ce document regroupe l'ensemble des diagrammes d'architecture et de modélisation à jour de l'application **Vite & Gourmand** (conformes au référentiel DWWM et au cahier des charges ECF) :
1. **Diagramme de Cas d'Utilisation (Use Case Diagram)**
2. **Modèle Conceptuel et Physique de Données Relationnelles (MCD / MLD / MPD — MySQL 8.0)**
3. **Schéma Documentaire NoSQL (MongoDB 7.0 — Statistiques Administrateur)**
4. **Diagramme de Classes UML (Architecture MVC / POO)**
5. **Diagrammes de Séquence UML** (Passation de commande, Suivi & Avis client, Réinitialisation de mot de passe)

---

## 1. Diagramme de Cas d'Utilisation (Use Case)

Conformément au cahier des charges :
- Le **Visiteur** peut consulter l'accueil, les horaires, les avis validés, filtrer/consulter les menus, envoyer un message de contact, créer un compte client (`/register`) ou réinitialiser son mot de passe (`/forgot-password`).
- Le **Client (`Utilisateur`)** hérite du Visiteur et peut passer commande, modifier son profil, suivre ses commandes, modifier/annuler une commande tant qu'elle est **`En attente`**, et déposer un unique avis (note 1 à 5 + commentaire) sur une commande **`Terminée`**.
- L'**Employé** peut gérer les menus, les plats, les horaires d'ouverture, filtrer et faire évoluer le statut des commandes (ou annuler avec motif et mode de contact client) et modérer les avis clients (`Validé` / `Refusé`).
- L'**Administrateur** hérite des droits de l'Employé et peut créer/désactiver des comptes **Employés** (jamais un compte Administrateur) ainsi que consulter les statistiques et le chiffre d'affaires par menu issus de la base **NoSQL MongoDB**.

```mermaid
flowchart LR
    Visiteur(["👤 Visiteur"])
    Client(["👤 Client (Utilisateur)"])
    Employe(["🧑‍🍳 Employé"])
    Admin(["👑 Administrateur"])

    Client -->|hérite| Visiteur
    Admin -->|hérite| Employe

    subgraph Public["Espace Public & Authentification"]
        UC_Home["Consulter l'accueil, les avis validés et les horaires"]
        UC_Menus["Consulter et filtrer les menus & allergènes"]
        UC_Contact["Envoyer un message via le formulaire de contact"]
        UC_Register["Créer un compte client (/register)"]
        UC_Login["Se connecter / Se déconnecter (/login)"]
        UC_Forgot["Réinitialiser son mot de passe (/forgot-password)"]
    end

    subgraph EspaceClient["Espace Client (/compte & /commande)"]
        UC_Order["Commander un menu avec calcul de distance et remise (-10%)"]
        UC_Profile["Modifier ses informations personnelles"]
        UC_Track["Consulter l'historique et le suivi horodaté de ses commandes"]
        UC_EditCancel["Modifier (hors menu) ou annuler une commande 'En attente'"]
        UC_Review["Déposer un avis (1 par commande 'Terminée')"]
    end

    subgraph EspaceEmploye["Espace Employé (/employe)"]
        UC_ManageOrders["Filtrer et mettre à jour le statut des commandes"]
        UC_CancelEmp["Annuler une commande (avec mode de contact et motif)"]
        UC_ModReviews["Valider ou refuser les avis clients"]
        UC_CrudMenus["Créer, modifier et supprimer des menus et des plats"]
        UC_Horaires["Modifier les horaires d'ouverture"]
    end

    subgraph EspaceAdmin["Espace Administrateur (/admin)"]
        UC_CreateEmp["Créer un compte Employé (notification mail sans mot de passe)"]
        UC_ToggleEmp["Activer / Désactiver un compte Employé"]
        UC_StatsNoSQL["Consulter les statistiques et le CA par menu (MongoDB NoSQL)"]
    end

    Visiteur --> UC_Home
    Visiteur --> UC_Menus
    Visiteur --> UC_Contact
    Visiteur --> UC_Register
    Visiteur --> UC_Login
    Visiteur --> UC_Forgot

    Client --> UC_Order
    Client --> UC_Profile
    Client --> UC_Track
    Client --> UC_EditCancel
    Client --> UC_Review

    Employe --> UC_ManageOrders
    Employe --> UC_CancelEmp
    Employe --> UC_ModReviews
    Employe --> UC_CrudMenus
    Employe --> UC_Horaires

    Admin --> UC_CreateEmp
    Admin --> UC_ToggleEmp
    Admin --> UC_StatsNoSQL
```

---

## 2. Modèle de Données Relationnelles (MCD / MLD / MPD — MySQL 8.0)

Le schéma relationnel comprend **12 tables** en 3ème forme normale (3NF) :
`roles`, `utilisateurs`, `themes`, `regimes`, `menus`, `photos_menu`, `plats`, `menus_plats`, `allergenes`, `plats_allergenes`, `commandes`, `commande_suivi`, `avis`, `horaires`.

```mermaid
erDiagram
    ROLES ||--o{ UTILISATEURS : "attribue"
    UTILISATEURS ||--o{ COMMANDES : "passe"
    UTILISATEURS ||--o{ AVIS : "redige"
    COMMANDES ||--o| AVIS : "1 avis max par commande"
    COMMANDES ||--o{ COMMANDE_SUIVI : "historise"
    MENUS ||--o{ COMMANDES : "concerne"
    THEMES ||--o{ MENUS : "categorise"
    REGIMES ||--o{ MENUS : "definit"
    MENUS ||--o{ PHOTOS_MENU : "illustre"
    MENUS ||--o{ MENUS_PLATS : "compose"
    PLATS ||--o{ MENUS_PLATS : "appartient"
    PLATS ||--o{ PLATS_ALLERGENES : "contient"
    ALLERGENES ||--o{ PLATS_ALLERGENES : "reference"

    ROLES {
        int id_role PK
        varchar libelle
    }

    UTILISATEURS {
        int id_utilisateur PK
        varchar nom
        varchar prenom
        varchar email
        varchar mot_de_passe
        varchar telephone
        varchar adresse
        int code_postal
        varchar ville
        varchar photo
        int role FK
        boolean actif
        varchar reset_token
        datetime reset_expires_at
    }

    THEMES {
        int id_theme PK
        varchar libelle
    }

    REGIMES {
        int id_regime PK
        varchar libelle
    }

    MENUS {
        int id_menu PK
        varchar titre
        varchar description
        int theme FK
        int regime FK
        int min_personnes
        decimal prix_personne
        int jours_avant
        int stock
    }

    PHOTOS_MENU {
        int id_photo PK
        int menu FK
        varchar chemin
        int ordre
    }

    PLATS {
        int id_plat PK
        varchar titre
        varchar description
        varchar type
    }

    ALLERGENES {
        int id_allergene PK
        varchar libelle
    }

    MENUS_PLATS {
        int menu PK, FK
        int plat PK, FK
    }

    PLATS_ALLERGENES {
        int plat PK, FK
        int allergene PK, FK
    }

    COMMANDES {
        int id_commande PK
        char order_token UK
        int utilisateur FK
        int menu FK
        date date_create
        date date_livraison
        time heure_livraison
        varchar adresse_livraison
        varchar ville_livraison
        varchar code_postal_livraison
        int qte_personnes
        decimal prix_menus
        decimal prix_livraison
        decimal reduction_groupe
        decimal frais_kilometriques
        decimal prix_total
        boolean pret_materiel
        boolean restitution
        varchar statut
        varchar mode_contact
        text motif_annulation
    }

    COMMANDE_SUIVI {
        int id_suivi PK
        int commande FK
        varchar statut
        datetime date_modification
    }

    AVIS {
        int id_avis PK
        int utilisateur FK
        int commande FK, UK
        date date_avis
        int note
        text message
        varchar statut
    }

    HORAIRES {
        int id_horaire PK
        varchar jour
        time heure_ouverture
        time heure_fermeture
        boolean ferme
    }
```

---

## 3. Schéma Documentaire NoSQL (MongoDB 7.0)

Pour répondre à l'exigence NoSQL du référentiel et du sujet ECF (comparaison du nombre de commandes par menu et calcul du chiffre d'affaires dans l'espace administrateur), la base MongoDB **`vite_et_gourmand_stats`** stocke les documents de commandes dans la collection **`orders_stats`** :

```json
{
  "_id": 11,
  "order_id": 11,
  "menu_id": 2,
  "menu_titre": "Élégance",
  "prix_personne": 45.00,
  "date_create": "2026-01-25",
  "date_livraison": "2026-02-12",
  "qte_personnes": 10,
  "prix_menus": 450.00,
  "prix_livraison": 5.00,
  "reduction_groupe": 0.00,
  "frais_kilometriques": 0.00,
  "prix_total": 455.00,
  "statut": "Terminée",
  "synced_at": "2026-11-04T18:00:00+00:00"
}
```

---

## 4. Diagramme de Classes UML (Architecture MVC / POO)

```mermaid
classDiagram
    direction TB

    class Router {
        -array routes
        +get(string path, callable action) void
        +post(string path, callable action) void
        +dispatch(string method, string uri) void
    }

    class Session {
        +start() void
        +isLoggedIn() bool
        +isAdmin() bool
        +isEmployee() bool
        +requireRoles(array roles) void
    }

    class Csrf {
        +generate() string
        +validate(?string token) bool
    }

    class Utilisateur {
        -int id
        -string lastName
        -string firstName
        -string email
        -string password
        -string phone
        -string address
        -int postalCode
        -string city
        -Role role
        -bool active
        +isActive() bool
    }

    class Menu {
        -int id
        -string title
        -string description
        -Theme theme
        -Regime regime
        -int minPeople
        -float unitPrice
        -int daysBefore
        -int stock
        -array dishes
        -array photos
    }

    class Commande {
        -int id
        -string orderToken
        -Utilisateur user
        -Menu menu
        -DateTimeImmutable createdAt
        -DateTimeImmutable deliveryDate
        -string deliveryTime
        -int peopleCount
        -float totalPrice
        -bool equipmentLoan
        -bool returned
        -string status
        -?string contactMode
        -?string cancellationReason
        -array statusHistory
    }

    class Avis {
        -int id
        -Utilisateur user
        -?int orderId
        -DateTimeImmutable date
        -int rating
        -string message
        -string status
    }

    class Horaire {
        -int id
        -string day
        -?string openingTime
        -?string closingTime
        -bool closed
        +getFormattedSlot() string
    }

    class UtilisateurRepository {
        +findByEmail(string email) ?Utilisateur
        +findById(int id) ?Utilisateur
        +findEmployees() array
        +createEmployee(...) int
        +createClient(...) int
        +updateProfile(...) bool
        +saveResetToken(...) bool
        +findByValidResetToken(string hash) ?Utilisateur
        +updatePasswordAndClearResetToken(...) bool
    }

    class CommandeRepository {
        +create(Commande commande) int
        +findAll() array
        +findById(int id) ?Commande
        +findByUserId(int userId) array
        +updateByClient(...) bool
        +cancelByClient(int orderId, int userId) bool
        +updateStatus(int id, string status, bool returned) bool
        +cancelByEmployee(int id, string mode, string reason) bool
        +addStatusHistory(int orderId, string status) void
        +getRevenueStats(?int menuId, ?string start, ?string end) array
    }

    class StatsNoSqlRepository {
        +syncFromRelational() array
        +getRevenueStats(?int menuId, ?string start, ?string end) array
    }

    class AuthController {
        +login(string email, string password) ?string
        +logout() string
        +register(...) array
        +requestPasswordReset(string email, string baseUrl) array
        +resetPassword(string token, string pass, string confirm) array
    }

    class CompteController {
        +updateProfile(...) array
        +updateOrderByClient(...) array
        +cancelOrderByClient(int userId, int orderId) array
        +submitReview(int userId, int orderId, int rating, string msg) array
    }

    class CommandeController {
        +getOrderData(int menuId, int userId) array
        +calculatePrice(int menuId, int people, float km) array
        +updateStatusByEmployee(int orderId, string status) bool
        +cancelOrderByEmployee(int orderId, string mode, string reason) bool
    }

    class AdminController {
        +getEmployees() array
        +createEmployee(...) array
        +toggleEmployeeStatus(int userId, bool active) array
        +getAnalyticsData(?int menuId, ?string start, ?string end) array
    }

    AuthController --> UtilisateurRepository
    CompteController --> UtilisateurRepository
    CompteController --> CommandeRepository
    AdminController --> UtilisateurRepository
    AdminController --> StatsNoSqlRepository
    CommandeController --> CommandeRepository
    CommandeRepository --> Commande
    UtilisateurRepository --> Utilisateur
```

---

## 5. Diagrammes de Séquence UML

### 5.1 Passation d'une commande avec calcul de distance, transaction SQL et synchronisation NoSQL

```mermaid
sequenceDiagram
    actor Client as Client connecté
    participant UI as Page /commande (JS)
    participant Router as public/index.php
    participant Dist as DistanceService (Google / BAN+OSRM)
    participant Svc as CommandeService
    participant DB as MySQL (PDO Transaction)
    participant NoSQL as MongoDB (StatsNoSqlRepository)
    participant Mail as MailService

    Client->>UI: Saisit l'adresse, la date et le nombre de personnes
    UI->>Router: POST /commande/distance (adresse, ville, code postal)
    Router->>Dist: getDistanceInKm(origin, destination)
    Dist-->>Router: distanceKm + ville résolue
    Router-->>UI: JSON { distanceKm }
    UI->>Router: POST /commande/calculate-price (menuId, people, distanceKm)
    Router->>Svc: calculatePrice(unitPrice, people, minPeople, distanceKm)
    Svc-->>Router: Tarifs détaillés (remise -10% si >= min+5, livraison 5€ + 0.59€/km hors Grenoble)
    Router-->>UI: JSON { prices }
    Client->>UI: Confirme la commande dans la modale récapitulative
    UI->>Router: POST /commande (avec csrf_token & order_token)
    Router->>DB: beginTransaction()
    Router->>DB: reserveStock(menuId, people)
    Router->>DB: INSERT INTO commandes + INSERT INTO commande_suivi ('En attente')
    Router->>DB: commit()
    Router->>NoSQL: syncFromRelational() (BulkWrite dans orders_stats)
    Router->>Mail: sendOrderConfirmation(email, orderId, totalPrice)
    Router-->>UI: JSON { success: true, orderId }
```

### 5.2 Suivi de commande par l'employé, notification client et dépôt d'avis unique

```mermaid
sequenceDiagram
    actor Employe as Employé
    actor Client as Client
    participant EmpUI as /employe
    participant CompteUI as /compte
    participant Ctrl as CommandeController / CompteController
    participant DB as MySQL (commandes, commande_suivi, avis)
    participant Mail as MailService

    Employe->>EmpUI: Passe la commande au statut "Terminée"
    EmpUI->>Ctrl: POST /employe/commandes/statut
    Ctrl->>DB: UPDATE commandes SET statut = 'Terminée'
    Ctrl->>DB: INSERT INTO commande_suivi ('Terminée', NOW())
    Ctrl->>Mail: sendOrderCompletedReviewNotice(email, firstName, orderId)
    Client->>CompteUI: Consulte ses commandes et clique sur "Donner mon avis"
    CompteUI->>Ctrl: POST /compte/avis/create (id_commande, note, message)
    Ctrl->>DB: Vérifie statut = 'Terminée' et !existsForOrder(orderId)
    Ctrl->>DB: INSERT INTO avis (utilisateur, commande, note, message, statut='En attente')
    Employe->>EmpUI: Consulte l'onglet Avis et clique sur "Valider"
    EmpUI->>DB: UPDATE avis SET statut = 'Validé' (publié sur l'accueil)
```

