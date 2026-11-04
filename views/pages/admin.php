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

            <?php if (!empty($adminFlash)): ?>
                <div
                    class="admin-flash <?= !empty($adminFlash['success']) ? 'admin-flash-success' : 'admin-flash-error' ?>"
                    role="status"
                >
                    <i class="fa-solid <?= !empty($adminFlash['success']) ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                    <span><?= htmlspecialchars((string) ($adminFlash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- Panneau 1 : Comptes employés -->
            <div
                id="admin-tab-employes"
                role="tabpanel"
                aria-labelledby="admin-tab-btn-employes"
                class="admin-panel <?= $activeTab === 'employes' ? 'active' : '' ?>"
            >
                <div class="employe-card">
                    <div class="employe-card-header employe-card-header-flex">
                        <div>
                            <h3 class="employe-card-title">Gestion des comptes employés (<?= count($employees ?? []) ?>)</h3>
                            <p class="employe-card-subtitle">
                                Créez de nouveaux accès employés (la création d'un compte administrateur n'est pas autorisée) et activez ou suspendez les comptes existants.
                            </p>
                        </div>
                        <button
                            type="button"
                            class="employe-action-btn btn-approve js-toggle-box"
                            data-target="create-employe-box"
                        >
                            <i class="fa-solid fa-user-plus"></i> Nouvel employé
                        </button>
                    </div>

                    <div class="employe-card-body">
                        <!-- Formulaire de création d'un compte employé -->
                        <div id="create-employe-box" class="employe-form-panel" hidden>
                            <h4 class="employe-form-title">Créer un compte employé</h4>
                            <p class="admin-form-note">
                                <i class="fa-solid fa-envelope"></i>
                                Un e-mail de notification sera envoyé automatiquement au collaborateur (sans son mot de passe, que vous lui communiquerez directement).
                            </p>
                            <form method="post" action="/admin/employes/create" class="employe-crud-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="employe-form-grid">
                                    <div class="employe-filter-group">
                                        <label for="new-emp-nom" class="employe-filter-label">Nom *</label>
                                        <input
                                            type="text"
                                            id="new-emp-nom"
                                            name="nom"
                                            class="employe-input"
                                            maxlength="50"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-emp-prenom" class="employe-filter-label">Prénom *</label>
                                        <input
                                            type="text"
                                            id="new-emp-prenom"
                                            name="prenom"
                                            class="employe-input"
                                            maxlength="50"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-emp-email" class="employe-filter-label">Adresse e-mail (identifiant) *</label>
                                        <input
                                            type="email"
                                            id="new-emp-email"
                                            name="email"
                                            class="employe-input"
                                            maxlength="100"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-emp-tel" class="employe-filter-label">Téléphone</label>
                                        <input
                                            type="text"
                                            id="new-emp-tel"
                                            name="telephone"
                                            class="employe-input"
                                            maxlength="50"
                                            placeholder="+33 6 12 34 56 78"
                                        >
                                    </div>

                                    <div class="employe-filter-group employe-span-2">
                                        <label for="new-emp-password" class="employe-filter-label">
                                            Mot de passe (min. 10 car., 1 maj., 1 min., 1 chiffre, 1 car. spécial) *
                                        </label>
                                        <input
                                            type="password"
                                            id="new-emp-password"
                                            name="password"
                                            class="employe-input"
                                            minlength="10"
                                            required
                                        >
                                    </div>

                                    <div class="employe-filter-group employe-span-2">
                                        <label class="employe-filter-label">Rôle attribué</label>
                                        <input
                                            type="text"
                                            class="employe-input"
                                            value="Employé (création de compte Administrateur non autorisée)"
                                            disabled
                                        >
                                    </div>
                                </div>

                                <div class="employe-form-submit-row">
                                    <button type="submit" class="employe-action-btn btn-approve">
                                        <i class="fa-solid fa-check"></i> Créer le compte employé
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des comptes employés -->
                        <?php if (empty($employees)): ?>
                            <p class="employe-placeholder">Aucun compte employé enregistré.</p>
                        <?php else: ?>
                            <div class="employe-orders-list">
                                <?php foreach ($employees as $employee): ?>
                                    <?php
                                        $empId = $employee->getId();
                                        $isActive = $employee->isActive();
                                    ?>
                                    <article class="employe-order-item">
                                        <div class="employe-order-header">
                                            <div>
                                                <h4 class="employe-order-title">
                                                    <?= htmlspecialchars($employee->getFirstName() . ' ' . $employee->getLastName(), ENT_QUOTES, 'UTF-8') ?>
                                                </h4>
                                                <span class="employe-order-sub">
                                                    <i class="fa-solid fa-envelope"></i>
                                                    <?= htmlspecialchars($employee->getEmail(), ENT_QUOTES, 'UTF-8') ?>
                                                    &nbsp;|&nbsp;
                                                    <i class="fa-solid fa-phone"></i>
                                                    <?= htmlspecialchars($employee->getPhone(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>

                                            <div class="employe-order-badges">
                                                <span class="employe-status-badge badge-encours">
                                                    <?= htmlspecialchars($employee->getRole()->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                                <span class="employe-status-badge <?= $isActive ? 'badge-valide' : 'badge-refuse' ?>">
                                                    <?= $isActive ? 'Compte actif' : 'Compte désactivé' ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="employe-avis-actions">
                                            <form method="post" action="/admin/employes/statut" class="employe-inline-form">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id_utilisateur" value="<?= (int) $empId ?>">
                                                <input type="hidden" name="actif" value="<?= $isActive ? '0' : '1' ?>">
                                                <?php if ($isActive): ?>
                                                    <button type="submit" class="employe-action-btn btn-reject">
                                                        <i class="fa-solid fa-user-slash"></i> Désactiver le compte
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="employe-action-btn btn-approve">
                                                        <i class="fa-solid fa-user-check"></i> Réactiver le compte
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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
