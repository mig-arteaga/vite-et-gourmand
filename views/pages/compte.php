<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Mon compte - Vite & Gourmand</title>
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

    <!-- Espace Client section -->
    <section class="compte-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">M</span>on
            </h2>
            <h1 class="headline title-up">Compte</h1>

            <!-- Navigation par onglets -->
            <div class="compte-tabs" role="tablist" aria-label="Sections de mon espace personnel">
                <button
                    type="button"
                    role="tab"
                    id="compte-tab-btn-commandes"
                    aria-controls="compte-tab-commandes"
                    aria-selected="<?= $activeTab === 'commandes' ? 'true' : 'false' ?>"
                    class="compte-tab-btn <?= $activeTab === 'commandes' ? 'active' : '' ?>"
                    data-compte-tab="commandes"
                >
                    <i class="fa-solid fa-receipt"></i>
                    <span>Mes commandes</span>
                </button>

                <button
                    type="button"
                    role="tab"
                    id="compte-tab-btn-profil"
                    aria-controls="compte-tab-profil"
                    aria-selected="<?= $activeTab === 'profil' ? 'true' : 'false' ?>"
                    class="compte-tab-btn <?= $activeTab === 'profil' ? 'active' : '' ?>"
                    data-compte-tab="profil"
                >
                    <i class="fa-solid fa-user-pen"></i>
                    <span>Mes informations personnelles</span>
                </button>
            </div>

            <?php if (!empty($compteFlash)): ?>
                <div
                    class="admin-flash <?= !empty($compteFlash['success']) ? 'admin-flash-success' : 'admin-flash-error' ?>"
                    role="status"
                >
                    <i class="fa-solid <?= !empty($compteFlash['success']) ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                    <span><?= htmlspecialchars((string) ($compteFlash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- Panneau 1 : Mes commandes -->
            <div
                id="compte-tab-commandes"
                role="tabpanel"
                aria-labelledby="compte-tab-btn-commandes"
                class="compte-panel <?= $activeTab === 'commandes' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Historique et suivi de mes commandes</h3>
                        <p class="employe-card-subtitle">
                            Suivez l'avancement de vos commandes en temps réel, modifiez ou annulez une commande en attente, et déposez votre avis une fois la prestation terminée.
                        </p>
                    </div>
                    <div class="employe-card-body">
                        <p class="employe-placeholder">Module de suivi et gestion des commandes client prêt à être connecté (Étape 12.4.2).</p>
                    </div>
                </div>
            </div>

            <!-- Panneau 2 : Mes informations personnelles -->
            <div
                id="compte-tab-profil"
                role="tabpanel"
                aria-labelledby="compte-tab-btn-profil"
                class="compte-panel <?= $activeTab === 'profil' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Mes informations personnelles</h3>
                        <p class="employe-card-subtitle">
                            Mettez à jour vos coordonnées afin qu'elles soient pré-remplies automatiquement lors de vos prochaines commandes.
                        </p>
                    </div>
                    <div class="employe-card-body">
                        <?php if ($currentUser === null): ?>
                            <p class="employe-placeholder">Impossible de charger vos informations.</p>
                        <?php else: ?>
                            <form method="post" action="/compte/profil/update" class="employe-crud-form compte-profile-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="employe-form-grid">
                                    <div class="employe-filter-group">
                                        <label for="profil-nom" class="employe-filter-label">Nom *</label>
                                        <input
                                            type="text"
                                            id="profil-nom"
                                            name="nom"
                                            class="employe-input"
                                            maxlength="50"
                                            value="<?= htmlspecialchars($currentUser->getLastName(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="profil-prenom" class="employe-filter-label">Prénom *</label>
                                        <input
                                            type="text"
                                            id="profil-prenom"
                                            name="prenom"
                                            class="employe-input"
                                            maxlength="50"
                                            value="<?= htmlspecialchars($currentUser->getFirstName(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="profil-email" class="employe-filter-label">Adresse e-mail *</label>
                                        <input
                                            type="email"
                                            id="profil-email"
                                            name="email"
                                            class="employe-input"
                                            maxlength="100"
                                            value="<?= htmlspecialchars($currentUser->getEmail(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="profil-telephone" class="employe-filter-label">Téléphone *</label>
                                        <input
                                            type="text"
                                            id="profil-telephone"
                                            name="telephone"
                                            class="employe-input"
                                            maxlength="50"
                                            value="<?= htmlspecialchars($currentUser->getPhone(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group employe-span-2">
                                        <label for="profil-adresse" class="employe-filter-label">Adresse postale *</label>
                                        <input
                                            type="text"
                                            id="profil-adresse"
                                            name="adresse"
                                            class="employe-input"
                                            maxlength="100"
                                            value="<?= htmlspecialchars($currentUser->getAddress(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="profil-cp" class="employe-filter-label">Code postal (5 chiffres) *</label>
                                        <input
                                            type="text"
                                            id="profil-cp"
                                            name="code_postal"
                                            class="employe-input"
                                            pattern="\d{5}"
                                            maxlength="5"
                                            value="<?= htmlspecialchars(str_pad((string) $currentUser->getPostalCode(), 5, '0', STR_PAD_LEFT), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="profil-ville" class="employe-filter-label">Ville *</label>
                                        <input
                                            type="text"
                                            id="profil-ville"
                                            name="ville"
                                            class="employe-input"
                                            maxlength="50"
                                            value="<?= htmlspecialchars($currentUser->getCity(), ENT_QUOTES, 'UTF-8') ?>"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="employe-form-submit-row">
                                    <button type="submit" class="employe-action-btn btn-approve">
                                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer mes informations
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
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
