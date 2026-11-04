<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Espace administrateur - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php
        require __DIR__ . '/../layouts/header.php';
    ?>

    <!-- Espace Administrateur section -->
    <section class="admin-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">E</span>space
            </h2>
            <h1 class="headline title-up">Administrateur</h1>

            <!-- Navigation par onglets + raccourci vers l'Espace Employé -->
            <div class="admin-tabs" role="tablist" aria-label="Sections de l'espace administrateur">
                <button
                    type="button"
                    role="tab"
                    id="admin-tab-btn-employes"
                    aria-controls="admin-tab-employes"
                    aria-selected="<?= $activeTab === 'employes' ? 'true' : 'false' ?>"
                    class="admin-tab-btn <?= $activeTab === 'employes' ? 'active' : '' ?>"
                    data-admin-tab="employes"
                >
                    <i class="fa-solid fa-user-shield"></i>
                    <span>Comptes employés</span>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="admin-tab-btn-statistiques"
                    aria-controls="admin-tab-statistiques"
                    aria-selected="<?= $activeTab === 'statistiques' ? 'true' : 'false' ?>"
                    class="admin-tab-btn <?= $activeTab === 'statistiques' ? 'active' : '' ?>"
                    data-admin-tab="statistiques"
                >
                    <i class="fa-solid fa-chart-column"></i>
                    <span>Statistiques & Chiffre d'affaires</span>
                </button>

                <a href="/employe" class="admin-shortcut-link">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>Accéder à l'espace employé</span>
                </a>
            </div>

            <!-- Panneau 1 : Comptes employés -->
            <div
                id="admin-tab-employes"
                role="tabpanel"
                aria-labelledby="admin-tab-btn-employes"
                class="admin-panel <?= $activeTab === 'employes' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Gestion des comptes employés</h3>
                        <p class="employe-card-subtitle">Créez de nouveaux accès employés et activez ou suspendez les comptes existants.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de gestion des comptes employés prêt à être connecté (Étape 12.3.3).</p>
                    </div>
                </div>
            </div>

            <!-- Panneau 2 : Statistiques & Chiffre d'affaires -->
            <div
                id="admin-tab-statistiques"
                role="tabpanel"
                aria-labelledby="admin-tab-btn-statistiques"
                class="admin-panel <?= $activeTab === 'statistiques' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Statistiques des commandes et chiffre d'affaires</h3>
                        <p class="employe-card-subtitle">Visualisez les ventes par menu sous forme de graphique et calculez le chiffre d'affaires sur la période de votre choix.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de statistiques et de calcul du chiffre d'affaires prêt à être connecté (Étape 12.3.4).</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require __DIR__ . '/../layouts/footer.php';
    ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>
