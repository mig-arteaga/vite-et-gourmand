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
                <!-- Sous-section 1 : Gestion des Menus -->
                <div class="employe-card">
                    <div class="employe-card-header employe-card-header-flex">
                        <div>
                            <h3 class="employe-card-title">Gestion des menus (<?= count($menus) ?>)</h3>
                            <p class="employe-card-subtitle">Créez de nouveaux menus ou modifiez les tarifs, stocks, plats associés et conditions des menus existants.</p>
                        </div>
                        <button
                            type="button"
                            class="employe-action-btn btn-approve js-toggle-box"
                            data-target="create-menu-box"
                        >
                            <i class="fa-solid fa-plus"></i> Nouveau menu
                        </button>
                    </div>

                    <div class="employe-card-body">
                        <!-- Formulaire de création d'un menu -->
                        <div id="create-menu-box" class="employe-form-panel" hidden>
                            <h4 class="employe-form-title">Créer un nouveau menu</h4>
                            <form method="post" action="/employe/menus/create" class="employe-crud-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="employe-form-grid">
                                    <div class="employe-filter-group">
                                        <label for="new-menu-titre" class="employe-filter-label">Titre du menu (max 50 car.) *</label>
                                        <input type="text" id="new-menu-titre" name="titre" class="employe-input" maxlength="50" required>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-theme" class="employe-filter-label">Thème *</label>
                                        <select id="new-menu-theme" name="theme" class="employe-select" required>
                                            <?php foreach ($themes as $theme): ?>
                                                <option value="<?= (int) $theme->getId() ?>">
                                                    <?= htmlspecialchars($theme->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-regime" class="employe-filter-label">Régime *</label>
                                        <select id="new-menu-regime" name="regime" class="employe-select" required>
                                            <?php foreach ($regimes as $regime): ?>
                                                <option value="<?= (int) $regime->getId() ?>">
                                                    <?= htmlspecialchars($regime->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-photo" class="employe-filter-label">Photo principale *</label>
                                        <select id="new-menu-photo" name="photo" class="employe-select" required>
                                            <?php foreach ($availablePhotos as $index => $photoPath): ?>
                                                <option value="<?= htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8') ?>">
                                                    Visuel menu n°<?= $index + 1 ?> (<?= htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8') ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-min-pers" class="employe-filter-label">Personnes min. *</label>
                                        <input type="number" id="new-menu-min-pers" name="min_personnes" class="employe-input" min="1" value="6" required>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-prix" class="employe-filter-label">Prix / personne (€) *</label>
                                        <input type="number" id="new-menu-prix" name="prix_personne" class="employe-input" min="1" step="0.50" value="30.00" required>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-delai" class="employe-filter-label">Délai min. (jours avant) *</label>
                                        <input type="number" id="new-menu-delai" name="jours_avant" class="employe-input" min="1" value="5" required>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-menu-stock" class="employe-filter-label">Stock disponible *</label>
                                        <input type="number" id="new-menu-stock" name="stock" class="employe-input" min="0" value="25" required>
                                    </div>
                                </div>

                                <div class="employe-filter-group employe-full-width">
                                    <label for="new-menu-desc" class="employe-filter-label">Description (max 255 car.) *</label>
                                    <textarea id="new-menu-desc" name="description" class="employe-textarea" maxlength="255" rows="2" required></textarea>
                                </div>

                                <div class="employe-filter-group employe-full-width">
                                    <span class="employe-filter-label">Plats composant ce menu</span>
                                    <div class="employe-checkbox-grid">
                                        <?php foreach ($dishes as $dish): ?>
                                            <label class="employe-checkbox-chip">
                                                <input type="checkbox" name="plats[]" value="<?= (int) $dish->getId() ?>">
                                                <span>
                                                    <strong>[<?= htmlspecialchars($dish->getType(), ENT_QUOTES, 'UTF-8') ?>]</strong>
                                                    <?= htmlspecialchars($dish->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="employe-form-submit-row">
                                    <button type="submit" class="employe-action-btn btn-approve">
                                        <i class="fa-solid fa-check"></i> Enregistrer le menu
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des menus existants -->
                        <div class="employe-orders-list">
                            <?php foreach ($menus as $menuItem): ?>
                                <?php
                                    $menuId = $menuItem->getId();
                                    $mainPhoto = $menuItem->getPhotos()[0]?->getPath() ?? 'images/menu-1-1.jpg';
                                    $menuDishIds = array_map(fn($d) => $d->getId(), $menuItem->getDishes());
                                ?>
                                <article class="employe-order-item">
                                    <div class="employe-menu-row">
                                        <img
                                            src="<?= htmlspecialchars($mainPhoto, ENT_QUOTES, 'UTF-8') ?>"
                                            alt="<?= htmlspecialchars($menuItem->getTitle(), ENT_QUOTES, 'UTF-8') ?>"
                                            class="employe-menu-thumb"
                                        >
                                        <div class="employe-menu-main">
                                            <div class="employe-order-header">
                                                <div>
                                                    <h4 class="employe-order-title">
                                                        <?= htmlspecialchars($menuItem->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                    </h4>
                                                    <span class="employe-order-sub">
                                                        Thème : <strong><?= htmlspecialchars($menuItem->getTheme()->getLabel(), ENT_QUOTES, 'UTF-8') ?></strong>
                                                        • Régime : <strong><?= htmlspecialchars($menuItem->getRegime()->getLabel(), ENT_QUOTES, 'UTF-8') ?></strong>
                                                        • Min. <?= (int) $menuItem->getMinPeople() ?> pers.
                                                        • Délai : <?= (int) $menuItem->getDaysBefore() ?> j
                                                    </span>
                                                </div>

                                                <div class="employe-order-badges">
                                                    <span class="employe-order-price">
                                                        <?= number_format($menuItem->getUnitPrice(), 2, ',', ' ') ?> € / pers.
                                                    </span>
                                                    <span class="employe-status-badge <?= $menuItem->getStock() > 0 ? 'badge-valide' : 'badge-refuse' ?>">
                                                        Stock : <?= (int) $menuItem->getStock() ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <p class="employe-avis-message">
                                                <?= htmlspecialchars($menuItem->getDescription(), ENT_QUOTES, 'UTF-8') ?>
                                            </p>

                                            <div class="employe-dish-tags">
                                                <?php foreach ($menuItem->getDishes() as $menuDish): ?>
                                                    <span class="employe-dish-tag">
                                                        <strong><?= htmlspecialchars($menuDish->getType(), ENT_QUOTES, 'UTF-8') ?> :</strong>
                                                        <?= htmlspecialchars($menuDish->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>

                                            <div class="employe-avis-actions">
                                                <div class="employe-inline-form">
                                                    <button
                                                        type="button"
                                                        class="employe-action-btn btn-edit js-toggle-box"
                                                        data-target="edit-menu-box-<?= (int) $menuId ?>"
                                                    >
                                                        <i class="fa-solid fa-pen"></i> Modifier
                                                    </button>

                                                    <form
                                                        method="post"
                                                        action="/employe/menus/delete"
                                                        class="employe-inline-form js-delete-form"
                                                        data-confirm-message="Êtes-vous sûr de vouloir supprimer le menu « <?= htmlspecialchars($menuItem->getTitle(), ENT_QUOTES, 'UTF-8') ?> » ?"
                                                    >
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id_menu" value="<?= (int) $menuId ?>">
                                                        <button type="submit" class="employe-action-btn btn-reject">
                                                            <i class="fa-solid fa-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Formulaire de modification du menu -->
                                    <div id="edit-menu-box-<?= (int) $menuId ?>" class="employe-form-panel" hidden>
                                        <h4 class="employe-form-title">Modifier « <?= htmlspecialchars($menuItem->getTitle(), ENT_QUOTES, 'UTF-8') ?> »</h4>
                                        <form method="post" action="/employe/menus/update" class="employe-crud-form">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id_menu" value="<?= (int) $menuId ?>">

                                            <div class="employe-form-grid">
                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Titre du menu *</label>
                                                    <input
                                                        type="text"
                                                        name="titre"
                                                        class="employe-input"
                                                        maxlength="50"
                                                        value="<?= htmlspecialchars($menuItem->getTitle(), ENT_QUOTES, 'UTF-8') ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Thème *</label>
                                                    <select name="theme" class="employe-select" required>
                                                        <?php foreach ($themes as $theme): ?>
                                                            <option
                                                                value="<?= (int) $theme->getId() ?>"
                                                                <?= $theme->getId() === $menuItem->getTheme()->getId() ? 'selected' : '' ?>
                                                            >
                                                                <?= htmlspecialchars($theme->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Régime *</label>
                                                    <select name="regime" class="employe-select" required>
                                                        <?php foreach ($regimes as $regime): ?>
                                                            <option
                                                                value="<?= (int) $regime->getId() ?>"
                                                                <?= $regime->getId() === $menuItem->getRegime()->getId() ? 'selected' : '' ?>
                                                            >
                                                                <?= htmlspecialchars($regime->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Photo principale *</label>
                                                    <select name="photo" class="employe-select" required>
                                                        <?php foreach ($availablePhotos as $index => $photoPath): ?>
                                                            <option
                                                                value="<?= htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8') ?>"
                                                                <?= $photoPath === $mainPhoto ? 'selected' : '' ?>
                                                            >
                                                                Visuel menu n°<?= $index + 1 ?> (<?= htmlspecialchars($photoPath, ENT_QUOTES, 'UTF-8') ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Personnes min. *</label>
                                                    <input
                                                        type="number"
                                                        name="min_personnes"
                                                        class="employe-input"
                                                        min="1"
                                                        value="<?= (int) $menuItem->getMinPeople() ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Prix / personne (€) *</label>
                                                    <input
                                                        type="number"
                                                        name="prix_personne"
                                                        class="employe-input"
                                                        min="1"
                                                        step="0.50"
                                                        value="<?= htmlspecialchars((string) $menuItem->getUnitPrice(), ENT_QUOTES, 'UTF-8') ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Délai min. (jours) *</label>
                                                    <input
                                                        type="number"
                                                        name="jours_avant"
                                                        class="employe-input"
                                                        min="1"
                                                        value="<?= (int) $menuItem->getDaysBefore() ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Stock disponible *</label>
                                                    <input
                                                        type="number"
                                                        name="stock"
                                                        class="employe-input"
                                                        min="0"
                                                        value="<?= (int) $menuItem->getStock() ?>"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <div class="employe-filter-group employe-full-width">
                                                <label class="employe-filter-label">Description (max 255 car.) *</label>
                                                <textarea
                                                    name="description"
                                                    class="employe-textarea"
                                                    maxlength="255"
                                                    rows="2"
                                                    required
                                                ><?= htmlspecialchars($menuItem->getDescription(), ENT_QUOTES, 'UTF-8') ?></textarea>
                                            </div>

                                            <div class="employe-filter-group employe-full-width">
                                                <span class="employe-filter-label">Plats associés</span>
                                                <div class="employe-checkbox-grid">
                                                    <?php foreach ($dishes as $dish): ?>
                                                        <label class="employe-checkbox-chip">
                                                            <input
                                                                type="checkbox"
                                                                name="plats[]"
                                                                value="<?= (int) $dish->getId() ?>"
                                                                <?= in_array($dish->getId(), $menuDishIds, true) ? 'checked' : '' ?>
                                                            >
                                                            <span>
                                                                <strong>[<?= htmlspecialchars($dish->getType(), ENT_QUOTES, 'UTF-8') ?>]</strong>
                                                                <?= htmlspecialchars($dish->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                            </span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="employe-form-submit-row">
                                                <button type="submit" class="employe-action-btn btn-approve">
                                                    <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Sous-section 2 : Gestion des Plats -->
                <div class="employe-card">
                    <div class="employe-card-header employe-card-header-flex">
                        <div>
                            <h3 class="employe-card-title">Gestion des plats (<?= count($dishes) ?>)</h3>
                            <p class="employe-card-subtitle">Gérez les entrées, plats et desserts ainsi que leurs allergènes associés.</p>
                        </div>
                        <button
                            type="button"
                            class="employe-action-btn btn-approve js-toggle-box"
                            data-target="create-plat-box"
                        >
                            <i class="fa-solid fa-plus"></i> Nouveau plat
                        </button>
                    </div>

                    <div class="employe-card-body">
                        <!-- Formulaire de création d'un plat -->
                        <div id="create-plat-box" class="employe-form-panel" hidden>
                            <h4 class="employe-form-title">Créer un nouveau plat</h4>
                            <form method="post" action="/employe/plats/create" class="employe-crud-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="employe-form-grid">
                                    <div class="employe-filter-group">
                                        <label for="new-plat-titre" class="employe-filter-label">Titre du plat (max 50 car.) *</label>
                                        <input type="text" id="new-plat-titre" name="titre" class="employe-input" maxlength="50" required>
                                    </div>

                                    <div class="employe-filter-group">
                                        <label for="new-plat-type" class="employe-filter-label">Catégorie *</label>
                                        <select id="new-plat-type" name="type" class="employe-select" required>
                                            <option value="Entrée">Entrée</option>
                                            <option value="Plat">Plat</option>
                                            <option value="Dessert">Dessert</option>
                                        </select>
                                    </div>

                                    <div class="employe-filter-group employe-span-2">
                                        <label for="new-plat-desc" class="employe-filter-label">Description courte (max 50 car.) *</label>
                                        <input type="text" id="new-plat-desc" name="description" class="employe-input" maxlength="50" required>
                                    </div>
                                </div>

                                <div class="employe-filter-group employe-full-width">
                                    <span class="employe-filter-label">Allergènes présents dans ce plat</span>
                                    <div class="employe-checkbox-grid">
                                        <?php foreach ($allergens as $allergen): ?>
                                            <label class="employe-checkbox-chip">
                                                <input type="checkbox" name="allergenes[]" value="<?= (int) $allergen->getId() ?>">
                                                <span><?= htmlspecialchars($allergen->getLabel(), ENT_QUOTES, 'UTF-8') ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="employe-form-submit-row">
                                    <button type="submit" class="employe-action-btn btn-approve">
                                        <i class="fa-solid fa-check"></i> Enregistrer le plat
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Liste des plats existants -->
                        <div class="employe-orders-list">
                            <?php foreach ($dishes as $dishItem): ?>
                                <?php
                                    $dishId = $dishItem->getId();
                                    $dishAllergenIds = array_map(fn($a) => $a->getId(), $dishItem->getAllergens());
                                ?>
                                <article class="employe-order-item">
                                    <div class="employe-order-header">
                                        <div>
                                            <h4 class="employe-order-title">
                                                <?= htmlspecialchars($dishItem->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                            </h4>
                                            <span class="employe-order-sub">
                                                <?= htmlspecialchars($dishItem->getDescription(), ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>

                                        <span class="employe-status-badge badge-encours">
                                            <?= htmlspecialchars($dishItem->getType(), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>

                                    <div class="employe-dish-tags">
                                        <?php if (empty($dishItem->getAllergens())): ?>
                                            <span class="employe-dish-tag">Aucun allergène déclaré</span>
                                        <?php else: ?>
                                            <?php foreach ($dishItem->getAllergens() as $dishAllergen): ?>
                                                <span class="employe-dish-tag">
                                                    <i class="fa-solid fa-wheat-awn-circle-exclamation"></i>
                                                    <?= htmlspecialchars($dishAllergen->getLabel(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>

                                    <div class="employe-avis-actions">
                                        <div class="employe-inline-form">
                                            <button
                                                type="button"
                                                class="employe-action-btn btn-edit js-toggle-box"
                                                data-target="edit-plat-box-<?= (int) $dishId ?>"
                                            >
                                                <i class="fa-solid fa-pen"></i> Modifier
                                            </button>

                                            <form
                                                method="post"
                                                action="/employe/plats/delete"
                                                class="employe-inline-form js-delete-form"
                                                data-confirm-message="Êtes-vous sûr de vouloir supprimer le plat « <?= htmlspecialchars($dishItem->getTitle(), ENT_QUOTES, 'UTF-8') ?> » ?"
                                            >
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="id_plat" value="<?= (int) $dishId ?>">
                                                <button type="submit" class="employe-action-btn btn-reject">
                                                    <i class="fa-solid fa-trash"></i> Supprimer
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Formulaire de modification du plat -->
                                    <div id="edit-plat-box-<?= (int) $dishId ?>" class="employe-form-panel" hidden>
                                        <h4 class="employe-form-title">Modifier « <?= htmlspecialchars($dishItem->getTitle(), ENT_QUOTES, 'UTF-8') ?> »</h4>
                                        <form method="post" action="/employe/plats/update" class="employe-crud-form">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="id_plat" value="<?= (int) $dishId ?>">

                                            <div class="employe-form-grid">
                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Titre du plat *</label>
                                                    <input
                                                        type="text"
                                                        name="titre"
                                                        class="employe-input"
                                                        maxlength="50"
                                                        value="<?= htmlspecialchars($dishItem->getTitle(), ENT_QUOTES, 'UTF-8') ?>"
                                                        required
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label class="employe-filter-label">Catégorie *</label>
                                                    <select name="type" class="employe-select" required>
                                                        <?php foreach (['Entrée', 'Plat', 'Dessert'] as $dishType): ?>
                                                            <option
                                                                value="<?= $dishType ?>"
                                                                <?= $dishItem->getType() === $dishType ? 'selected' : '' ?>
                                                            >
                                                                <?= $dishType ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="employe-filter-group employe-span-2">
                                                    <label class="employe-filter-label">Description courte (max 50 car.) *</label>
                                                    <input
                                                        type="text"
                                                        name="description"
                                                        class="employe-input"
                                                        maxlength="50"
                                                        value="<?= htmlspecialchars($dishItem->getDescription(), ENT_QUOTES, 'UTF-8') ?>"
                                                        required
                                                    >
                                                </div>
                                            </div>

                                            <div class="employe-filter-group employe-full-width">
                                                <span class="employe-filter-label">Allergènes</span>
                                                <div class="employe-checkbox-grid">
                                                    <?php foreach ($allergens as $allergen): ?>
                                                        <label class="employe-checkbox-chip">
                                                            <input
                                                                type="checkbox"
                                                                name="allergenes[]"
                                                                value="<?= (int) $allergen->getId() ?>"
                                                                <?= in_array($allergen->getId(), $dishAllergenIds, true) ? 'checked' : '' ?>
                                                            >
                                                            <span><?= htmlspecialchars($allergen->getLabel(), ENT_QUOTES, 'UTF-8') ?></span>
                                                        </label>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>

                                            <div class="employe-form-submit-row">
                                                <button type="submit" class="employe-action-btn btn-approve">
                                                    <i class="fa-solid fa-floppy-disk"></i> Enregistrer les modifications
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
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
                        <?php if (empty($horaires)): ?>
                            <p class="employe-placeholder">Aucun horaire configuré.</p>
                        <?php else: ?>
                            <form method="post" action="/employe/horaires/update" class="employe-crud-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                                <div class="employe-horaires-list">
                                    <?php foreach ($horaires as $horaire): ?>
                                        <?php
                                            $horaireId = $horaire->getId();
                                            $isClosed = $horaire->isClosed();
                                        ?>
                                        <div class="employe-horaire-row js-horaire-row">
                                            <div class="employe-horaire-day">
                                                <strong><?= htmlspecialchars($horaire->getDay(), ENT_QUOTES, 'UTF-8') ?></strong>
                                                <span class="employe-status-badge <?= $isClosed ? 'badge-refuse' : 'badge-valide' ?>">
                                                    <?= htmlspecialchars($horaire->getFormattedSlot(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>

                                            <div class="employe-horaire-inputs">
                                                <div class="employe-filter-group">
                                                    <label
                                                        for="horaire-open-<?= (int) $horaireId ?>"
                                                        class="employe-filter-label"
                                                    >
                                                        Ouverture
                                                    </label>
                                                    <input
                                                        type="time"
                                                        id="horaire-open-<?= (int) $horaireId ?>"
                                                        name="horaires[<?= (int) $horaireId ?>][heure_ouverture]"
                                                        class="employe-input js-horaire-time"
                                                        value="<?= htmlspecialchars($horaire->getOpeningTimeForInput(), ENT_QUOTES, 'UTF-8') ?>"
                                                        <?= $isClosed ? 'disabled' : 'required' ?>
                                                    >
                                                </div>

                                                <div class="employe-filter-group">
                                                    <label
                                                        for="horaire-close-<?= (int) $horaireId ?>"
                                                        class="employe-filter-label"
                                                    >
                                                        Fermeture
                                                    </label>
                                                    <input
                                                        type="time"
                                                        id="horaire-close-<?= (int) $horaireId ?>"
                                                        name="horaires[<?= (int) $horaireId ?>][heure_fermeture]"
                                                        class="employe-input js-horaire-time"
                                                        value="<?= htmlspecialchars($horaire->getClosingTimeForInput(), ENT_QUOTES, 'UTF-8') ?>"
                                                        <?= $isClosed ? 'disabled' : 'required' ?>
                                                    >
                                                </div>

                                                <label class="employe-checkbox-chip employe-horaire-closed-chip">
                                                    <input
                                                        type="checkbox"
                                                        name="horaires[<?= (int) $horaireId ?>][ferme]"
                                                        value="1"
                                                        class="js-horaire-closed"
                                                        <?= $isClosed ? 'checked' : '' ?>
                                                    >
                                                    <span>Fermé toute la journée</span>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="employe-form-submit-row">
                                    <button type="submit" class="employe-action-btn btn-approve">
                                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer les horaires
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale de confirmation de suppression -->
        <div
            id="employe-confirm-modal"
            class="employe-modal-backdrop"
            role="dialog"
            aria-modal="true"
            aria-labelledby="employe-confirm-title"
            hidden
        >
            <div class="employe-modal-box">
                <h3 id="employe-confirm-title" class="employe-modal-title">
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmer la suppression
                </h3>
                <p id="employe-confirm-message" class="employe-modal-message">
                    Êtes-vous sûr de vouloir supprimer cet élément ?
                </p>
                <div class="employe-modal-actions">
                    <button type="button" id="employe-confirm-cancel" class="employe-action-btn btn-cancel-modal">
                        Annuler
                    </button>
                    <button type="button" id="employe-confirm-submit" class="employe-action-btn btn-reject">
                        <i class="fa-solid fa-trash"></i> Confirmer la suppression
                    </button>
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
