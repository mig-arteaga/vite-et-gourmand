<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Réinitialiser mon mot de passe - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Main section -->
    <section class="main-section">
        <img src="images/hero.jpg" alt="" class="parallax">
        <div class="container">
            <div class="login-box">
                <form class="login-form" method="POST" action="/reset-password">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(\App\Core\Csrf::generate(), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <input
                        type="hidden"
                        name="token"
                        value="<?= htmlspecialchars((string) ($resetToken ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="login-head login-head-compact">
                        <a href="/">
                            <img src="images/vg-logo-2.svg" alt="Logo Vite & Gourmand">
                        </a>
                        <h4>Nouveau mot de passe</h4>
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
                        <?php if (empty($isValidToken)): ?>
                            <div class="auth-flash auth-flash-error" role="alert">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>Ce lien de réinitialisation est invalide ou a expiré. Veuillez effectuer une nouvelle demande.</span>
                            </div>
                            <div class="login-buttons">
                                <a href="/forgot-password" class="btn body-btn btn-underline">
                                    Nouvelle demande
                                </a>
                                <a href="/login" class="btn body-btn btn-underline">
                                    Connexion
                                </a>
                            </div>
                        <?php else: ?>
                            <label for="reset-password"><h4>Nouveau mot de passe *</h4></label>
                            <input
                                type="password"
                                id="reset-password"
                                name="password"
                                minlength="10"
                                required
                            >

                            <label for="reset-password-confirm"><h4>Confirmer le mot de passe *</h4></label>
                            <input
                                type="password"
                                id="reset-password-confirm"
                                name="password_confirm"
                                minlength="10"
                                required
                            >

                            <p class="auth-password-hint">
                                <i class="fa-solid fa-shield-halved"></i>
                                10 caractères minimum : au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial.
                            </p>

                            <div class="login-buttons">
                                <button type="submit" class="btn body-btn btn-underline">
                                    Valider
                                </button>
                                <a href="/login" class="btn body-btn btn-underline">
                                    Annuler
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script type="module" src="js/main.js"></script>
</body>
</html>

