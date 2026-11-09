<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <span>Statistiques</span>
                </button>

                <a href="/employe" class="admin-shortcut-link">
                    <span>Espace employé</span>
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
                            <!-- <p class="employe-card-subtitle">
                                Créez de nouveaux accès employés et activez ou suspendez les comptes existants.
                            </p> -->
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
                                        <i class="fa-solid fa-check"></i> Créer
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
                                                    <?= $isActive ? 'Actif' : 'Désactivé' ?>
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
                                                        <i class="fa-solid fa-user-slash"></i> Désactiver
                                                    </button>
                                                <?php else: ?>
                                                    <button type="submit" class="employe-action-btn btn-approve">
                                                        <i class="fa-solid fa-user-check"></i> Réactiver
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
                <?php
                    $statsEngine = $analytics['engine'] ?? 'NoSQL';
                    $filters = $analytics['filters'] ?? ['menuId' => null, 'startDate' => '', 'endDate' => ''];
                    $chartStats = $analytics['chartStats'] ?? [];
                    $filteredStats = $analytics['filteredStats'] ?? [];
                    $summary = $analytics['summary'] ?? [
                        'totalRevenue' => 0.0,
                        'totalOrders' => 0,
                        'totalPeople' => 0,
                        'averageBasket' => 0.0,
                        'maxOrders' => 1,
                        'maxRevenue' => 1.0
                    ];
                ?>

                <!-- Filtres de calcul du chiffre d'affaires -->
                <div class="employe-card">
                    <div class="employe-card-header employe-card-header-flex">
                        <div>
                            <h3 class="employe-card-title">Calcul du chiffre d'affaires</h3>
                            <!-- <p class="employe-card-subtitle">
                                Filtrez les commandes par menu et/ou par période de livraison pour calculer le chiffre d'affaires correspondant.
                            </p> -->
                        </div>
                        <span class="employe-status-badge badge-encours" title="Source de données statistiques">
                            <i class="fa-solid fa-database"></i> Source NoSQL : <?= htmlspecialchars((string) $statsEngine, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                    <div class="employe-card-body">
                        <form method="get" action="/admin" class="admin-stats-filter-form">
                            <input type="hidden" name="tab" value="statistiques">

                            <div class="employe-filter-group">
                                <label for="stats-menu-id" class="employe-filter-label">Menu</label>
                                <select id="stats-menu-id" name="menu_id" class="employe-select">
                                    <option value="">Tous les menus</option>
                                    <?php foreach (($menus ?? []) as $menuOption): ?>
                                        <option
                                            value="<?= (int) $menuOption->getId() ?>"
                                            <?= $filters['menuId'] === $menuOption->getId() ? 'selected' : '' ?>
                                        >
                                            <?= htmlspecialchars($menuOption->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="employe-filter-group">
                                <label for="stats-date-debut" class="employe-filter-label">Date de début</label>
                                <input
                                    type="date"
                                    id="stats-date-debut"
                                    name="date_debut"
                                    class="employe-input"
                                    value="<?= htmlspecialchars((string) $filters['startDate'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>

                            <div class="employe-filter-group">
                                <label for="stats-date-fin" class="employe-filter-label">Date de fin</label>
                                <input
                                    type="date"
                                    id="stats-date-fin"
                                    name="date_fin"
                                    class="employe-input"
                                    value="<?= htmlspecialchars((string) $filters['endDate'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>

                            <div class="admin-stats-filter-actions">
                                <button type="submit" class="employe-action-btn btn-approve">
                                    <i class="fa-solid fa-calculator"></i> Calculer
                                </button>
                                <a href="/admin?tab=statistiques" class="employe-action-btn btn-cancel-modal admin-reset-link">
                                    <i class="fa-solid fa-rotate-left"></i> Réinitialiser
                                </a>
                            </div>
                        </form>

                        <!-- Indicateurs clés (KPI) -->
                        <div class="admin-kpi-grid">
                            <div class="admin-kpi-card">
                                <span class="admin-kpi-label">Chiffre d'affaires (sélection)</span>
                                <strong class="admin-kpi-value">
                                    <?= number_format((float) $summary['totalRevenue'], 2, ',', ' ') ?> €
                                </strong>
                            </div>

                            <div class="admin-kpi-card">
                                <span class="admin-kpi-label">Commandes validées</span>
                                <strong class="admin-kpi-value">
                                    <?= (int) $summary['totalOrders'] ?>
                                </strong>
                            </div>

                            <div class="admin-kpi-card">
                                <span class="admin-kpi-label">Convives servis</span>
                                <strong class="admin-kpi-value">
                                    <?= (int) $summary['totalPeople'] ?> pers.
                                </strong>
                            </div>

                            <div class="admin-kpi-card">
                                <span class="admin-kpi-label">Panier moyen</span>
                                <strong class="admin-kpi-value">
                                    <?= number_format((float) $summary['averageBasket'], 2, ',', ' ') ?> €
                                </strong>
                            </div>
                        </div>

                        <!-- Tableau détaillé du chiffre d'affaires par menu -->
                        <div class="admin-table-wrapper">
                            <table class="admin-stats-table">
                                <thead>
                                    <tr>
                                        <th>Menu</th>
                                        <th>Prix / pers.</th>
                                        <th>Commandes</th>
                                        <th>Total convives</th>
                                        <th>Chiffre d'affaires</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($filteredStats as $statRow): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars((string) $statRow['titre'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>
                                            <td><?= number_format((float) $statRow['prix_personne'], 2, ',', ' ') ?> €</td>
                                            <td><?= (int) $statRow['nb_commandes'] ?></td>
                                            <td><?= (int) $statRow['total_personnes'] ?> pers.</td>
                                            <td class="admin-td-revenue">
                                                <?= number_format((float) $statRow['chiffre_affaires'], 2, ',', ' ') ?> €
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Graphique comparatif du nombre de commandes par menu -->
                <div class="employe-card">
                    <div class="employe-card-header">
                        <h3 class="employe-card-title">Comparatif graphique des commandes par menu</h3>
                        <p class="employe-card-subtitle">
                            Visualisation du nombre de commandes et de la répartition des ventes entre vos différents menus.
                        </p>
                    </div>
                    <div class="employe-card-body">
                        <div class="admin-chart-list" role="img" aria-label="Graphique à barres du nombre de commandes par menu">
                            <?php foreach ($chartStats as $chartRow): ?>
                                <?php
                                    $orderCount = (int) $chartRow['nb_commandes'];
                                    $revenueAmount = (float) $chartRow['chiffre_affaires'];
                                    $barPercent = $summary['maxOrders'] > 0
                                        ? round(($orderCount / $summary['maxOrders']) * 100)
                                        : 0;
                                    $isSelectedMenu = $filters['menuId'] === (int) $chartRow['id_menu'];
                                ?>
                                <div class="admin-chart-row <?= $isSelectedMenu ? 'admin-chart-row-highlight' : '' ?>">
                                    <div class="admin-chart-label-row">
                                        <span class="admin-chart-menu-name">
                                            <?= htmlspecialchars((string) $chartRow['titre'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="admin-chart-metrics">
                                            <strong><?= $orderCount ?> commande<?= $orderCount > 1 ? 's' : '' ?></strong>
                                            (<?= number_format($revenueAmount, 2, ',', ' ') ?> €)
                                        </span>
                                    </div>
                                    <div class="admin-chart-track">
                                        <div
                                            class="admin-chart-bar"
                                            style="width: <?= max($orderCount > 0 ? 8 : 0, (int) $barPercent) ?>%;"
                                        ></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
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
