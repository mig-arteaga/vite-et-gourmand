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
                        <!-- Filtres par statut d'avis -->
                        <div class="employe-filter-bar" role="group" aria-label="Filtrer les avis par statut">
                            <button type="button" class="employe-filter-chip active" data-avis-filter="all">
                                Tous (<?= count($reviews) ?>)
                            </button>
                            <button type="button" class="employe-filter-chip" data-avis-filter="En attente">
                                En attente
                            </button>
                            <button type="button" class="employe-filter-chip" data-avis-filter="Validé">
                                Validés
                            </button>
                            <button type="button" class="employe-filter-chip" data-avis-filter="Refusé">
                                Refusés
                            </button>
                        </div>

                        <?php if (empty($reviews)): ?>
                            <p class="employe-placeholder">Aucun avis client enregistré pour le moment.</p>
                        <?php else: ?>
                            <div class="employe-avis-list" id="employe-avis-list">
                                <?php foreach ($reviews as $review): ?>
                                    <?php
                                        $statusClass = match ($review->getStatus()) {
                                            'Validé' => 'badge-valide',
                                            'Refusé' => 'badge-refuse',
                                            default => 'badge-attente'
                                        };
                                    ?>
                                    <article
                                        class="employe-avis-item"
                                        data-avis-status="<?= htmlspecialchars($review->getStatus(), ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        <div class="employe-avis-top">
                                            <div class="employe-avis-author">
                                                <img
                                                    src="<?= htmlspecialchars($review->getUser()->getPhoto() ?: 'images/profile-0.jpg', ENT_QUOTES, 'UTF-8') ?>"
                                                    alt="Photo de <?= htmlspecialchars($review->getUser()->getFirstName(), ENT_QUOTES, 'UTF-8') ?>"
                                                    class="employe-avis-avatar"
                                                >
                                                <div>
                                                    <h4 class="employe-avis-name">
                                                        <?= htmlspecialchars($review->getUser()->getFirstName() . ' ' . $review->getUser()->getLastName(), ENT_QUOTES, 'UTF-8') ?>
                                                    </h4>
                                                    <span class="employe-avis-meta">
                                                        <?= htmlspecialchars($review->getUser()->getEmail(), ENT_QUOTES, 'UTF-8') ?>
                                                        • <?= htmlspecialchars($review->getDate()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="employe-avis-badges">
                                                <div class="employe-avis-stars" aria-label="Note de <?= (int) $review->getRating() ?> sur 5">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa-solid fa-star <?= $i <= $review->getRating() ? 'star-filled' : 'star-empty' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="employe-status-badge <?= $statusClass ?>">
                                                    <?= htmlspecialchars($review->getStatus(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </div>

                                        <p class="employe-avis-message">
                                            <?= nl2br(htmlspecialchars($review->getMessage(), ENT_QUOTES, 'UTF-8')) ?>
                                        </p>

                                        <div class="employe-avis-actions">
                                            <form method="post" action="/employe/avis/statut" class="employe-inline-form">
                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                                                >
                                                <input
                                                    type="hidden"
                                                    name="id_avis"
                                                    value="<?= (int) $review->getId() ?>"
                                                >
                                                <?php if ($review->getStatus() !== 'Validé'): ?>
                                                    <button
                                                        type="submit"
                                                        name="statut"
                                                        value="Validé"
                                                        class="employe-action-btn btn-approve"
                                                    >
                                                        <i class="fa-solid fa-check"></i> Valider
                                                    </button>
                                                <?php endif; ?>

                                                <?php if ($review->getStatus() !== 'Refusé'): ?>
                                                    <button
                                                        type="submit"
                                                        name="statut"
                                                        value="Refusé"
                                                        class="employe-action-btn btn-reject"
                                                    >
                                                        <i class="fa-solid fa-xmark"></i> Refuser
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
