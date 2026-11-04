<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Connexion - Vite & Gourmand</title>
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
            <!-- Login form -->
            <div class="login-box">
                <form class="login-form" method="POST" action="/login">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(\App\Core\Csrf::generate(), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="login-head">
                        <a href="/">
                            <img src="images/vg-logo-2.svg" alt="Logo Vite & Gourmand">
                        </a>
                        <h4>Connexion</h4>
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
                        <label for="login-email"><h4>Email</h4></label>
                        <input
                            type="email"
                            id="login-email"
                            name="email"
                            value="<?= htmlspecialchars((string) ($oldEmail ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                            required
                        >

                        <label for="login-password"><h4>Mot de passe</h4></label>
                        <input
                            type="password"
                            id="login-password"
                            name="password"
                            required
                        >

                        <div class="auth-forgot-row">
                            <a href="/forgot-password" class="auth-sublink">
                                Mot de passe oublié ?
                            </a>
                        </div>

                        <div class="login-buttons">
                            <button type="submit" class="btn body-btn btn-underline">
                                Se connecter
                            </button>
                            <a href="/" class="btn body-btn btn-underline">
                                Accueil
                            </a>
                        </div>

                        <div class="auth-switch-row">
                            <span>Pas encore de compte ?</span>
                            <a href="/register" class="auth-sublink">Créer un compte</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script type="module" src="js/main.js"></script>
</body>
</html>
