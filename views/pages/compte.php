<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <span>Commandes</span>
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
                    <span>Informations personnelles</span>
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
                    <div class="employe-card-header employe-card-header-flex">
                        <div>
                            <h3 class="employe-card-title">Historique et suivi de mes commandes (<?= count($clientOrders ?? []) ?>)</h3>
                            <p class="employe-card-subtitle">
                                Suivez l'avancement de vos commandes en temps réel, modifiez ou annulez une commande tant qu'elle est « En attente », et déposez votre avis une fois la prestation « Terminée ».
                            </p>
                        </div>
                        <a href="/menus" class="employe-action-btn btn-approve admin-reset-link">
                            <i class="fa-solid fa-utensils"></i> Commander un menu
                        </a>
                    </div>
                    <div class="employe-card-body">
                        <?php if (empty($clientOrders)): ?>
                            <p class="employe-placeholder">Vous n'avez pas encore passé de commande.</p>
                        <?php else: ?>
                            <div class="employe-orders-list">
                                <?php foreach ($clientOrders as $order): ?>
                                    <?php
                                        $orderId = $order->getId();
                                        $status = $order->getStatus();
                                        $menuObj = $order->getMenu();
                                        $history = $order->getStatusHistory();

                                        $badgeClass = 'badge-attente';
                                        if ($status === 'Terminée') {
                                            $badgeClass = 'badge-valide';
                                        } elseif ($status === 'Annulée') {
                                            $badgeClass = 'badge-refuse';
                                        } elseif ($status !== 'En attente') {
                                            $badgeClass = 'badge-encours';
                                        }

                                        $minDeliveryDate = (new DateTimeImmutable('today'))
                                            ->modify('+' . $menuObj->getDaysBefore() . ' days')
                                            ->format('Y-m-d');
                                        $maxPeopleForEdit = $menuObj->getStock() + $order->getPeopleCount();
                                    ?>
                                    <article class="employe-order-item">
                                        <!-- En-tête de la commande -->
                                        <div class="employe-order-header">
                                            <div>
                                                <h4 class="employe-order-title">
                                                    Commande #<?= (int) $orderId ?> — <?= htmlspecialchars($menuObj->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                                </h4>
                                                <span class="employe-order-sub">
                                                    Passée le <?= htmlspecialchars($order->getCreatedAt()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                                    &nbsp;|&nbsp;
                                                    Livraison prévue le <?= htmlspecialchars($order->getDeliveryDate()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                                    à <?= htmlspecialchars($order->getDeliveryTime(), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>

                                            <div class="employe-order-badges">
                                                <span class="employe-order-price">
                                                    <?= number_format($order->getTotalPrice(), 2, ',', ' ') ?> €
                                                </span>
                                                <span class="employe-status-badge <?= $badgeClass ?>">
                                                    <?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Détails en 3 colonnes -->
                                        <div class="employe-order-grid">
                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title">
                                                    <i class="fa-solid fa-location-dot"></i> Lieu et prestation
                                                </h5>
                                                <p><strong>Menu :</strong> <?= htmlspecialchars($menuObj->getTitle(), ENT_QUOTES, 'UTF-8') ?></p>
                                                <p><strong>Convives :</strong> <?= (int) $order->getPeopleCount() ?> personnes</p>
                                                <p>
                                                    <strong>Adresse :</strong>
                                                    <?= htmlspecialchars($order->getDeliveryAddress() . ', ' . $order->getDeliveryPostalCode() . ' ' . $order->getDeliveryCity(), ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                                <p>
                                                    <strong>Prêt de matériel :</strong>
                                                    <?= $order->hasEquipmentLoan() ? 'Oui' : 'Non' ?>
                                                    <?php if ($order->hasEquipmentLoan()): ?>
                                                        (<?= $order->isReturned() ? 'Restitué' : 'Non restitué' ?>)
                                                    <?php endif; ?>
                                                </p>
                                            </div>

                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title">
                                                    <i class="fa-solid fa-calculator"></i> Détail du tarif
                                                </h5>
                                                <p><strong>Menus :</strong> <?= number_format($order->getMenuPrice(), 2, ',', ' ') ?> €</p>
                                                <?php if ($order->getGroupDiscount() > 0): ?>
                                                    <p><strong>Réduction groupe (-10%) :</strong> -<?= number_format($order->getGroupDiscount(), 2, ',', ' ') ?> €</p>
                                                <?php endif; ?>
                                                <p><strong>Livraison :</strong> <?= number_format($order->getDeliveryPrice(), 2, ',', ' ') ?> €</p>
                                                <?php if ($order->getDistanceFee() > 0): ?>
                                                    <p><strong>Frais kilométriques :</strong> <?= number_format($order->getDistanceFee(), 2, ',', ' ') ?> €</p>
                                                <?php endif; ?>
                                                <p><strong>Total TTC :</strong> <?= number_format($order->getTotalPrice(), 2, ',', ' ') ?> €</p>
                                            </div>

                                            <div class="employe-order-box">
                                                <h5 class="employe-order-box-title">
                                                    <i class="fa-solid fa-clock-rotate-left"></i> Suivi de commande
                                                </h5>
                                                <?php if (empty($history)): ?>
                                                    <p class="employe-history-empty">Aucun suivi enregistré.</p>
                                                <?php else: ?>
                                                    <ul class="employe-history-list">
                                                        <?php foreach ($history as $step): ?>
                                                            <li>
                                                                <span class="history-status"><?= htmlspecialchars($step['statut'], ENT_QUOTES, 'UTF-8') ?></span>
                                                                <span class="history-date"><?= htmlspecialchars($step['date_modification']->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?></span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if ($status === 'Annulée' && $order->getCancelReason()): ?>
                                            <div class="employe-cancel-info">
                                                <strong>Motif d'annulation :</strong>
                                                <?= htmlspecialchars($order->getCancelReason(), ENT_QUOTES, 'UTF-8') ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Actions disponibles si la commande est toujours 'En attente' -->
                                        <?php if ($status === 'En attente'): ?>
                                            <div class="employe-order-actions">
                                                <span class="compte-order-hint">
                                                    <i class="fa-solid fa-circle-info"></i>
                                                    Votre commande est en attente de validation : vous pouvez encore la modifier ou l'annuler.
                                                </span>

                                                <div class="employe-inline-form">
                                                    <button
                                                        type="button"
                                                        class="employe-action-btn btn-edit js-toggle-box"
                                                        data-target="edit-client-order-<?= (int) $orderId ?>"
                                                    >
                                                        <i class="fa-solid fa-pen"></i> Modifier ma commande
                                                    </button>

                                                    <form
                                                        method="post"
                                                        action="/compte/commandes/cancel"
                                                        class="employe-inline-form js-delete-form"
                                                        data-confirm-message="Êtes-vous sûr de vouloir annuler votre commande #<?= (int) $orderId ?> (« <?= htmlspecialchars($menuObj->getTitle(), ENT_QUOTES, 'UTF-8') ?> ») ?"
                                                    >
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id_commande" value="<?= (int) $orderId ?>">
                                                        <button type="submit" class="employe-action-btn btn-reject">
                                                            <i class="fa-solid fa-ban"></i> Annuler ma commande
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            <!-- Formulaire dépliable de modification de la commande (tout sauf le menu) -->
                                            <div id="edit-client-order-<?= (int) $orderId ?>" class="employe-form-panel" hidden>
                                                <h4 class="employe-form-title">
                                                    Modifier ma commande #<?= (int) $orderId ?>
                                                </h4>
                                                <p class="admin-form-note">
                                                    <i class="fa-solid fa-utensils"></i>
                                                    Menu commandé (non modifiable) : <strong><?= htmlspecialchars($menuObj->getTitle(), ENT_QUOTES, 'UTF-8') ?></strong>
                                                    (<?= number_format($menuObj->getUnitPrice(), 2, ',', ' ') ?> € / pers. — min. <?= (int) $menuObj->getDaysBefore() ?> jours de délai).
                                                </p>

                                                <form method="post" action="/compte/commandes/update" class="employe-crud-form">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                    <input type="hidden" name="id_commande" value="<?= (int) $orderId ?>">

                                                    <div class="employe-form-grid">
                                                        <div class="employe-filter-group">
                                                            <label class="employe-filter-label">Date de livraison *</label>
                                                            <input
                                                                type="date"
                                                                name="date_livraison"
                                                                class="employe-input"
                                                                min="<?= htmlspecialchars($minDeliveryDate, ENT_QUOTES, 'UTF-8') ?>"
                                                                value="<?= htmlspecialchars($order->getDeliveryDate()->format('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group">
                                                            <label class="employe-filter-label">Heure de livraison (08h30 - 22h30) *</label>
                                                            <input
                                                                type="time"
                                                                name="heure_livraison"
                                                                class="employe-input"
                                                                min="08:30"
                                                                max="22:30"
                                                                value="<?= htmlspecialchars($order->getDeliveryTime(), ENT_QUOTES, 'UTF-8') ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group">
                                                            <label class="employe-filter-label">
                                                                Nombre de personnes (min. <?= (int) $menuObj->getMinPeople() ?>) *
                                                            </label>
                                                            <input
                                                                type="number"
                                                                name="qte_personnes"
                                                                class="employe-input"
                                                                min="<?= (int) $menuObj->getMinPeople() ?>"
                                                                max="<?= (int) $maxPeopleForEdit ?>"
                                                                value="<?= (int) $order->getPeopleCount() ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group">
                                                            <label class="employe-filter-label">Code postal (5 chiffres) *</label>
                                                            <input
                                                                type="text"
                                                                name="code_postal_livraison"
                                                                class="employe-input"
                                                                pattern="\d{5}"
                                                                maxlength="5"
                                                                value="<?= htmlspecialchars($order->getDeliveryPostalCode(), ENT_QUOTES, 'UTF-8') ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group employe-span-2">
                                                            <label class="employe-filter-label">Adresse de livraison *</label>
                                                            <input
                                                                type="text"
                                                                name="adresse_livraison"
                                                                class="employe-input"
                                                                maxlength="255"
                                                                value="<?= htmlspecialchars($order->getDeliveryAddress(), ENT_QUOTES, 'UTF-8') ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group">
                                                            <label class="employe-filter-label">Ville de livraison *</label>
                                                            <input
                                                                type="text"
                                                                name="ville_livraison"
                                                                class="employe-input"
                                                                maxlength="100"
                                                                value="<?= htmlspecialchars($order->getDeliveryCity(), ENT_QUOTES, 'UTF-8') ?>"
                                                                required
                                                            >
                                                        </div>

                                                        <div class="employe-filter-group">
                                                            <span class="employe-filter-label">Option matériel</span>
                                                            <label class="employe-checkbox-chip">
                                                                <input
                                                                    type="checkbox"
                                                                    name="pret_materiel"
                                                                    value="1"
                                                                    <?= $order->hasEquipmentLoan() ? 'checked' : '' ?>
                                                                >
                                                                <span>Prêt de matériel</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="employe-form-submit-row">
                                                        <button type="submit" class="employe-action-btn btn-approve">
                                                            <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php elseif ($status === 'Terminée'): ?>
                                            <?php $alreadyReviewed = !empty($reviewedOrderIds[$orderId]); ?>
                                            <?php if ($alreadyReviewed): ?>
                                                <div class="employe-order-actions">
                                                    <span class="compte-order-hint">
                                                        <i class="fa-solid fa-circle-check"></i>
                                                        Vous avez déjà déposé un avis pour cette commande. Merci pour votre retour !
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <!-- Action disponible si la commande est 'Terminée' et non encore évaluée : donner un avis -->
                                                <div class="employe-order-actions">
                                                    <span class="compte-order-hint">
                                                        <i class="fa-solid fa-star"></i>
                                                        Cette prestation est terminée : partagez votre expérience avec notre équipe !
                                                    </span>

                                                    <button
                                                        type="button"
                                                        class="employe-action-btn btn-approve js-toggle-box"
                                                        data-target="review-client-order-<?= (int) $orderId ?>"
                                                    >
                                                        <i class="fa-solid fa-comment-dots"></i> Donner mon avis
                                                    </button>
                                                </div>

                                                <!-- Formulaire dépliable de dépôt d'avis (note de 1 à 5 + commentaire) -->
                                                <div id="review-client-order-<?= (int) $orderId ?>" class="employe-form-panel" hidden>
                                                    <h4 class="employe-form-title">
                                                        Laisser un avis sur la commande #<?= (int) $orderId ?> (« <?= htmlspecialchars($menuObj->getTitle(), ENT_QUOTES, 'UTF-8') ?> »)
                                                    </h4>
                                                    <form method="post" action="/compte/avis/create" class="employe-crud-form">
                                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                                        <input type="hidden" name="id_commande" value="<?= (int) $orderId ?>">

                                                        <div class="employe-filter-group">
                                                            <label for="review-note-<?= (int) $orderId ?>" class="employe-filter-label">
                                                                Votre note (de 1 à 5) *
                                                            </label>
                                                            <select
                                                                id="review-note-<?= (int) $orderId ?>"
                                                                name="note"
                                                                class="employe-select employe-select-inline"
                                                                required
                                                            >
                                                                <option value="5">★★★★★ — 5 / 5 (Excellent)</option>
                                                                <option value="4">★★★★☆ — 4 / 5 (Très bien)</option>
                                                                <option value="3">★★★☆☆ — 3 / 5 (Satisfaisant)</option>
                                                                <option value="2">★★☆☆☆ — 2 / 5 (Moyen)</option>
                                                                <option value="1">★☆☆☆☆ — 1 / 5 (Insatisfaisant)</option>
                                                            </select>
                                                        </div>

                                                        <div class="employe-filter-group employe-full-width">
                                                            <label for="review-message-<?= (int) $orderId ?>" class="employe-filter-label">
                                                                Votre commentaire *
                                                            </label>
                                                            <textarea
                                                                id="review-message-<?= (int) $orderId ?>"
                                                                name="message"
                                                                class="employe-textarea"
                                                                rows="3"
                                                                maxlength="1000"
                                                                placeholder="Décrivez votre expérience (qualité des plats, ponctualité, service...)"
                                                                required
                                                            ></textarea>
                                                        </div>

                                                        <div class="employe-form-submit-row">
                                                            <button type="submit" class="employe-action-btn btn-approve">
                                                                <i class="fa-solid fa-paper-plane"></i> Envoyer mon avis
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

                <!-- Historique des avis déposés par le client -->
                <?php if (!empty($clientReviews)): ?>
                    <div class="employe-card">
                        <div class="employe-card-header">
                            <h3 class="employe-card-title">Mes avis déposés (<?= count($clientReviews) ?>)</h3>
                            <p class="employe-card-subtitle">
                                Retrouvez ici les avis que vous avez partagés suite à vos prestations.
                            </p>
                        </div>
                        <div class="employe-card-body">
                            <div class="employe-avis-list">
                                <?php foreach ($clientReviews as $review): ?>
                                    <article class="employe-avis-item">
                                        <div class="employe-avis-top">
                                            <div>
                                                <span class="employe-avis-meta">
                                                    <?php if ($review->getOrderId() !== null): ?>
                                                        Commande #<?= (int) $review->getOrderId() ?> •
                                                    <?php endif; ?>
                                                    Déposé le <?= htmlspecialchars($review->getDate()->format('d/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                                </span>
                                            </div>
                                            <div class="employe-avis-badges">
                                                <div class="employe-avis-stars" aria-label="Note de <?= (int) $review->getRating() ?> sur 5">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fa-solid fa-star <?= $i <= $review->getRating() ? 'star-filled' : 'star-empty' ?>"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="employe-avis-message">
                                            « <?= htmlspecialchars($review->getMessage(), ENT_QUOTES, 'UTF-8') ?> »
                                        </p>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
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
                                        <i class="fa-solid fa-floppy-disk"></i> Enregistrer
                                    </button>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modale de confirmation d'annulation -->
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
                    <i class="fa-solid fa-triangle-exclamation"></i> Confirmer l'annulation
                </h3>
                <p id="employe-confirm-message" class="employe-modal-message">
                    Êtes-vous sûr de vouloir annuler cette commande ?
                </p>
                <div class="employe-modal-actions">
                    <button type="button" id="employe-confirm-cancel" class="employe-action-btn btn-cancel-modal">
                        Retour
                    </button>
                    <button type="button" id="employe-confirm-submit" class="employe-action-btn btn-reject">
                        <i class="fa-solid fa-ban"></i> Confirmer l'annulation
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
