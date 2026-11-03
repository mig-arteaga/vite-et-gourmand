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
                        <p class="employe-card-subtitle">Consultez, filtrez par statut ou par client, et mettez à jour l'avancement des commandes.</p>
                    </div>
                    <div class="employe-card-body">
                        <!-- Barre de filtres des commandes (Statut + Client) -->
                        <div class="employe-orders-filters">
                            <div class="employe-filter-group">
                                <label for="employe-order-status-filter" class="employe-filter-label">Filtrer par statut</label>
                                <select id="employe-order-status-filter" class="employe-select">
                                    <option value="all">Tous les statuts (<?= count($orders) ?>)</option>
                                    <?php foreach ($allowedOrderStatuses as $workflowStatus): ?>
                                        <option value="<?= htmlspecialchars($workflowStatus, ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($workflowStatus, ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="Annulée">Annulée</option>
                                </select>
                            </div>

                            <div class="employe-filter-group">
                                <label for="employe-order-client-filter" class="employe-filter-label">Rechercher un client (nom ou email)</label>
                                <input
                                    type="search"
                                    id="employe-order-client-filter"
                                    class="employe-input"
                                    placeholder="Ex : Josh Peck, j.peck@mail.com..."
                                >
                            </div>
                        </div>

                        <?php if (empty($orders)): ?>
                            <p class="employe-placeholder">Aucune commande enregistrée pour le moment.</p>
                        <?php else: ?>
                            <div class="employe-orders-list" id="employe-orders-list">
                                <?php foreach ($orders as $order): ?>
                                    <?php
                                        $orderStatus = $order->getStatus();
                                        $orderBadgeClass = match ($orderStatus) {
                                            'Terminée' => 'badge-valide',
                                            'Annulée' => 'badge-refuse',
                                            'Accepté', 'En préparation', 'En cours de livraison', 'Livré' => 'badge-encours',
                                            default => 'badge-attente'
                                        };
                                        $clientFullName = $order->getUser()->getFirstName() . ' ' . $order->getUser()->getLastName();
                                        $clientSearchData = mb_strtolower($clientFullName . ' ' . $order->getUser()->getEmail(), 'UTF-8');
                                    ?>
                                    <article
                                        class="employe-order-item"
                                        data-order-status="<?= htmlspecialchars($orderStatus, ENT_QUOTES, 'UTF-8') ?>"
                                        data-order-client="<?= htmlspecialchars($clientSearchData, ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        <div class="employe-order-header">
                                            <div>
                                                <h4 class="employe-order-title">
                                                    Commande n°<?= (int) $order->getId() ?> — <?= htmlspecialchars($order->getMenu()->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                </h4>
                                                <span class="employe-order-sub">
                                                    Créée le <?= htmlspecialchars($order->getCreatedAt()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                                    • Prestation le <strong><?= htmlspecialchars($order->getDeliveryDate()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?></strong>
                                                    à <strong><?= htmlspecialchars($order->getDeliveryTime(), ENT_QUOTES, 'UTF-8') ?></strong>
                                                </span>
                                            </div>

                                            <div class="employe-order-badges">
                                                <span class="employe-order-price">
                                                    <?= number_format($order->getTotalPrice(), 2, ',', ' ') ?> €
                                                </span>
                                                <span class="employe-status-badge <?= $orderBadgeClass ?>">
                                                    <?= htmlspecialchars($orderStatus, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </div>

                                        <div class="employe-order-grid">
                                            <!-- Colonne Client -->
                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title"><i class="fa-solid fa-user"></i> Client</h5>
                                                <p><strong><?= htmlspecialchars($clientFullName, ENT_QUOTES, 'UTF-8') ?></strong></p>
                                                <p>Email : <?= htmlspecialchars($order->getUser()->getEmail(), ENT_QUOTES, 'UTF-8') ?></p>
                                                <p>GSM : <?= htmlspecialchars($order->getUser()->getPhone(), ENT_QUOTES, 'UTF-8') ?></p>
                                            </div>

                                            <!-- Colonne Livraison & Prestation -->
                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title"><i class="fa-solid fa-truck"></i> Livraison & Détails</h5>
                                                <p>
                                                    Adresse : <?= htmlspecialchars($order->getDeliveryAddress(), ENT_QUOTES, 'UTF-8') ?>,
                                                    <?= htmlspecialchars($order->getDeliveryPostalCode(), ENT_QUOTES, 'UTF-8') ?>
                                                    <?= htmlspecialchars($order->getDeliveryCity(), ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                                <p>Convives : <strong><?= (int) $order->getPeopleCount() ?> personnes</strong></p>
                                                <p>
                                                    Prêt de matériel :
                                                    <strong><?= $order->hasEquipmentLoan() ? 'Oui' : 'Non' ?></strong>
                                                    <?php if ($order->hasEquipmentLoan()): ?>
                                                        (Restitué : <strong><?= $order->isReturned() ? 'Oui' : 'Non' ?></strong>)
                                                    <?php endif; ?>
                                                </p>
                                            </div>

                                            <!-- Colonne Suivi Historique -->
                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title"><i class="fa-solid fa-clock-rotate-left"></i> Historique du suivi</h5>
                                                <?php if (empty($order->getStatusHistory())): ?>
                                                    <p class="employe-history-empty">Aucun historique enregistré.</p>
                                                <?php else: ?>
                                                    <ul class="employe-history-list">
                                                        <?php foreach ($order->getStatusHistory() as $step): ?>
                                                            <li>
                                                                <span class="history-status"><?= htmlspecialchars($step['statut'], ENT_QUOTES, 'UTF-8') ?></span>
                                                                <span class="history-date"><?= htmlspecialchars($step['date_modification']->format('d/m/Y à H:i'), ENT_QUOTES, 'UTF-8') ?></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if ($orderStatus === 'Annulée' && ($order->getContactMode() || $order->getCancelReason())): ?>
                                            <div class="employe-cancel-info">
                                                <strong><i class="fa-solid fa-ban"></i> Commande annulée</strong>
                                                — Contact client : <em><?= htmlspecialchars((string) $order->getContactMode(), ENT_QUOTES, 'UTF-8') ?></em>
                                                | Motif : <?= htmlspecialchars((string) $order->getCancelReason(), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($orderStatus !== 'Annulée'): ?>
                                            <div class="employe-order-actions">
                                                <!-- Formulaire de mise à jour du statut -->
                                                <form method="post" action="/employe/commandes/statut" class="employe-status-form">
                                                    <input
                                                        type="hidden"
                                                        name="csrf_token"
                                                        value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        name="id_commande"
                                                        value="<?= (int) $order->getId() ?>"
                                                    >

                                                    <label for="statut-commande-<?= (int) $order->getId() ?>" class="employe-inline-label">
                                                        Statut :
                                                    </label>
                                                    <select
                                                        id="statut-commande-<?= (int) $order->getId() ?>"
                                                        name="statut"
                                                        class="employe-select employe-select-inline"
                                                    >
                                                        <?php foreach ($allowedOrderStatuses as $statusOption): ?>
                                                            <?php
                                                                if ($statusOption === 'En attente du retour de matériel' && !$order->hasEquipmentLoan()) {
                                                                    continue;
                                                                }
                                                            ?>
                                                            <option
                                                                value="<?= htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8') ?>"
                                                                <?= $orderStatus === $statusOption ? 'selected' : '' ?>
                                                            >
                                                                <?= htmlspecialchars($statusOption, ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>

                                                    <?php if ($order->hasEquipmentLoan()): ?>
                                                        <label class="employe-checkbox-label">
                                                            <input
                                                                type="checkbox"
                                                                name="restitution"
                                                                value="1"
                                                                <?= $order->isReturned() ? 'checked' : '' ?>
                                                            >
                                                            <span>Matériel restitué</span>
                                                        </label>
                                                    <?php endif; ?>

                                                    <button type="submit" class="employe-action-btn btn-approve">
                                                        <i class="fa-solid fa-floppy-disk"></i> Mettre à jour
                                                    </button>
                                                </form>

                                                <?php if ($orderStatus !== 'Terminée'): ?>
                                                    <button
                                                        type="button"
                                                        class="employe-action-btn btn-reject js-toggle-cancel"
                                                        data-target="cancel-box-<?= (int) $order->getId() ?>"
                                                    >
                                                        <i class="fa-solid fa-ban"></i> Annuler la commande
                                                    </button>
                                                <?php endif; ?>
                                            </div>

                                            <?php if ($orderStatus !== 'Terminée'): ?>
                                                <!-- Formulaire d'annulation (nécessite contact client + motif selon l'énoncé) -->
                                                <div id="cancel-box-<?= (int) $order->getId() ?>" class="employe-cancel-box" hidden>
                                                    <form method="post" action="/employe/commandes/annuler" class="employe-cancel-form">
                                                        <input
                                                            type="hidden"
                                                            name="csrf_token"
                                                            value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>"
                                                        >
                                                        <input
                                                            type="hidden"
                                                            name="id_commande"
                                                            value="<?= (int) $order->getId() ?>"
                                                        >
                                                        <p class="employe-cancel-warning">
                                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                                            Conformément à la procédure, vous devez avoir contacté le client (GSM ou mail) avant d'annuler la commande.
                                                        </p>
                                                        <div class="employe-cancel-fields">
                                                            <div class="employe-filter-group">
                                                                <label for="mode-contact-<?= (int) $order->getId() ?>" class="employe-filter-label">
                                                                    Mode de contact utilisé *
                                                                </label>
                                                                <select
                                                                    id="mode-contact-<?= (int) $order->getId() ?>"
                                                                    name="mode_contact"
                                                                    class="employe-select"
                                                                    required
                                                                >
                                                                    <option value="">-- Sélectionner --</option>
                                                                    <option value="Appel GSM">Appel GSM</option>
                                                                    <option value="Mail">Mail</option>
                                                                </select>
                                                            </div>

                                                            <div class="employe-filter-group employe-cancel-reason-group">
                                                                <label for="motif-annulation-<?= (int) $order->getId() ?>" class="employe-filter-label">
                                                                    Motif d'annulation *
                                                                </label>
                                                                <input
                                                                    type="text"
                                                                    id="motif-annulation-<?= (int) $order->getId() ?>"
                                                                    name="motif_annulation"
                                                                    class="employe-input"
                                                                    placeholder="Précisez la raison de l'annulation convenue avec le client..."
                                                                    required
                                                                >
                                                            </div>

                                                            <button type="submit" class="employe-action-btn btn-reject">
                                                                Confirmer l'annulation
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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
