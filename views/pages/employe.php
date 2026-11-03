<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Espace employé - Vite & Gourmand</title>
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

    <!-- Espace Employé section -->
    <section class="employe-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">E</span>space
            </h2>
            <h1 class="headline title-up">Employé</h1>

            <!-- Navigation par onglets -->
            <div class="employe-tabs" role="tablist" aria-label="Sections de l'espace employé">
                <button
                    type="button"
                    role="tab"
                    id="tab-btn-commandes"
                    aria-controls="tab-commandes"
                    aria-selected="<?= $activeTab === 'commandes' ? 'true' : 'false' ?>"
                    class="employe-tab-btn <?= $activeTab === 'commandes' ? 'active' : '' ?>"
                    data-tab="commandes"
                >
                    <i class="fa-solid fa-receipt"></i>
                    <span>Commandes</span>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="tab-btn-avis"
                    aria-controls="tab-avis"
                    aria-selected="<?= $activeTab === 'avis' ? 'true' : 'false' ?>"
                    class="employe-tab-btn <?= $activeTab === 'avis' ? 'active' : '' ?>"
                    data-tab="avis"
                >
                    <i class="fa-solid fa-star"></i>
                    <span>Avis clients</span>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="tab-btn-menus"
                    aria-controls="tab-menus"
                    aria-selected="<?= $activeTab === 'menus' ? 'true' : 'false' ?>"
                    class="employe-tab-btn <?= $activeTab === 'menus' ? 'active' : '' ?>"
                    data-tab="menus"
                >
                    <i class="fa-solid fa-utensils"></i>
                    <span>Menus & Plats</span>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="tab-btn-horaires"
                    aria-controls="tab-horaires"
                    aria-selected="<?= $activeTab === 'horaires' ? 'true' : 'false' ?>"
                    class="employe-tab-btn <?= $activeTab === 'horaires' ? 'active' : '' ?>"
                    data-tab="horaires"
                >
                    <i class="fa-solid fa-clock"></i>
                    <span>Horaires</span>
                </button>
            </div>

            <!-- Panneau 1 : Commandes -->
            <div
                id="tab-commandes"
                role="tabpanel"
                aria-labelledby="tab-btn-commandes"
                class="employe-panel <?= $activeTab === 'commandes' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Suivi et traitement des commandes</h3>
                        <p class="employe-card-subtitle">Consultez, filtrez et mettez à jour le statut des commandes clients.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de gestion des commandes prêt à être connecté (Étape 12.2.4).</p>
                    </div>
                </div>
            </div>

            <!-- Panneau 2 : Avis clients -->
            <div
                id="tab-avis"
                role="tabpanel"
                aria-labelledby="tab-btn-avis"
                class="employe-panel <?= $activeTab === 'avis' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Consultation et modération des avis</h3>
                        <p class="employe-card-subtitle">Validez ou refusez les avis déposés par les clients avant leur publication sur l'accueil.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de modération des avis prêt à être connecté (Étape 12.2.3).</p>
                    </div>
                </div>
            </div>

            <!-- Panneau 3 : Menus & Plats -->
            <div
                id="tab-menus"
                role="tabpanel"
                aria-labelledby="tab-btn-menus"
                class="employe-panel <?= $activeTab === 'menus' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Gestion des menus et des plats</h3>
                        <p class="employe-card-subtitle">Créez, modifiez ou supprimez les menus, les stocks et les plats proposés.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de gestion des menus et plats prêt à être connecté (Étape 12.2.5).</p>
                    </div>
                </div>
            </div>

            <!-- Panneau 4 : Horaires -->
            <div
                id="tab-horaires"
                role="tabpanel"
                aria-labelledby="tab-btn-horaires"
                class="employe-panel <?= $activeTab === 'horaires' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Gestion des horaires d'ouverture</h3>
                        <p class="employe-card-subtitle">Mettez à jour les horaires d'ouverture affichés dans le pied de page du site.</p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de gestion des horaires prêt à être connecté (Étape 12.2.6).</p>
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
