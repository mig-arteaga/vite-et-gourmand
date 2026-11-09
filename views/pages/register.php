<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Inscription - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Main section -->
    <section class="main-section auth-page-section">
        <img src="images/hero.jpg" alt="" class="parallax">
        <div class="container">
            <div class="login-box">
                <form class="login-form login-form-wide" method="POST" action="/register">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(\App\Core\Csrf::generate(), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="login-head login-head-compact">
                        <a href="/">
                            <img src="images/vg-logo-2.svg" alt="Logo Vite & Gourmand">
                        </a>
                        <h4>Créer un compte</h4>
                    </div>

                    <?php if (!empty($authFlash)): ?>
                        <div
                            class="auth-flash <?= !empty($authFlash['success']) ? 'auth-flash-success' : 'auth-flash-error' ?>"
                            role="status"
                        >
                            <i class="fa-solid <?= !empty($authFlash['success']) ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                            <span><?= htmlspecialchars((string) ($authFlash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="login-content">
                        <div class="auth-form-grid">
                            <div class="auth-field">
                                <label for="reg-nom"><h4>Nom *</h4></label>
                                <input
                                    type="text"
                                    id="reg-nom"
                                    name="nom"
                                    maxlength="50"
                                    value="<?= htmlspecialchars((string) ($oldInput['nom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-prenom"><h4>Prénom *</h4></label>
                                <input
                                    type="text"
                                    id="reg-prenom"
                                    name="prenom"
                                    maxlength="50"
                                    value="<?= htmlspecialchars((string) ($oldInput['prenom'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-email"><h4>Email *</h4></label>
                                <input
                                    type="email"
                                    id="reg-email"
                                    name="email"
                                    maxlength="100"
                                    value="<?= htmlspecialchars((string) ($oldInput['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-telephone"><h4>Téléphone *</h4></label>
                                <input
                                    type="text"
                                    id="reg-telephone"
                                    name="telephone"
                                    maxlength="50"
                                    placeholder="Ex : 06 12 34 56 78"
                                    value="<?= htmlspecialchars((string) ($oldInput['telephone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field auth-span-2">
                                <label for="reg-adresse"><h4>Adresse postale *</h4></label>
                                <input
                                    type="text"
                                    id="reg-adresse"
                                    name="adresse"
                                    maxlength="100"
                                    value="<?= htmlspecialchars((string) ($oldInput['adresse'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-cp"><h4>Code postal (5 chiffres) *</h4></label>
                                <input
                                    type="text"
                                    id="reg-cp"
                                    name="code_postal"
                                    pattern="\d{5}"
                                    maxlength="5"
                                    value="<?= htmlspecialchars((string) ($oldInput['code_postal'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-ville"><h4>Ville *</h4></label>
                                <input
                                    type="text"
                                    id="reg-ville"
                                    name="ville"
                                    maxlength="50"
                                    value="<?= htmlspecialchars((string) ($oldInput['ville'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-password"><h4>Mot de passe *</h4></label>
                                <input
                                    type="password"
                                    id="reg-password"
                                    name="password"
                                    minlength="10"
                                    required
                                >
                            </div>

                            <div class="auth-field">
                                <label for="reg-password-confirm"><h4>Confirmer le mot de passe *</h4></label>
                                <input
                                    type="password"
                                    id="reg-password-confirm"
                                    name="password_confirm"
                                    minlength="10"
                                    required
                                >
                            </div>
                        </div>

                        <p class="auth-password-hint">
                            <i class="fa-solid fa-shield-halved"></i>
                            Sécurité : 10 caractères minimum comprenant au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.
                        </p>

                        <div class="login-buttons">
                            <a href="/login" class="btn body-btn btn-underline">
                                Déjà inscrit ?
                            </a>
                            <button type="submit" class="btn body-btn btn-underline">
                                S'inscrire
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script type="module" src="js/main.js"></script>
</body>
</html>

