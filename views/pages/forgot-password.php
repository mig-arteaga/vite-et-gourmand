<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <title>Mot de passe oublié - Vite & Gourmand</title>
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
                <form class="login-form" method="POST" action="/forgot-password">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(\App\Core\Csrf::generate(), ENT_QUOTES, 'UTF-8') ?>"
                    >
                    <div class="login-head login-head-compact">
                        <a href="/">
                            <img src="images/vg-logo-2.svg" alt="Logo Vite & Gourmand">
                        </a>
                        <h4>Mot de passe oublié</h4>
                    </div>

                    <?php if (!empty($authFlash)): ?>
                        <div
                            class="auth-flash <?= !empty($authFlash['success']) ? 'auth-flash-success' : 'auth-flash-error' ?>"
                            role="status"
                        >
                            <i class="fa-solid <?= !empty($authFlash['success']) ? 'fa-circle-check' : 'fa-circle-exclamation' ?>"></i>
                            <div>
                                <span><?= htmlspecialchars((string) ($authFlash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if (!empty($authFlash['demo_reset_url'])): ?>
                                    <div class="auth-demo-reset">
                                        <a href="<?= htmlspecialchars((string) $authFlash['demo_reset_url'], ENT_QUOTES, 'UTF-8') ?>" class="auth-sublink">
                                            <i class="fa-solid fa-key"></i> Ouvrir le lien de réinitialisation (aperçu local)
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="login-content">
                        <p class="auth-password-hint">
                            Saisissez l'adresse e-mail associée à votre compte. Nous vous enverrons un lien sécurisé pour définir un nouveau mot de passe.
                        </p>

                        <label for="forgot-email"><h4>Email</h4></label>
                        <input
                            type="email"
                            id="forgot-email"
                            name="email"
                            required
                        >

                        <div class="login-buttons">
                            <button type="submit" class="btn body-btn btn-underline">
                                Envoyer le lien
                            </button>
                            <a href="/login" class="btn body-btn btn-underline">
                                Retour connexion
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <script type="module" src="js/main.js"></script>
</body>
</html>

