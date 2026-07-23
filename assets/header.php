<?php
require_once "session.php";
?>

<!-- Header -->
<header class="header-2">
    <div class="container">
        <nav class="nav">
            <a href="index.php" class="logo-link">
                <img src="images/vg-logo-1.png" alt="" class="title-img">
            </a>
            <ul class="nav-list" id="nav-list">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">Accueil</a>
                </li>
                <li class="nav-item">
                    <a href="menus.php" class="nav-link">Menus</a>
                </li>
                <li class="nav-item">
                    <a href="nous.php" class="nav-link">Nous</a>
                </li>
                <li class="nav-item">
                    <a href="contact.php" class="nav-link">Contact</a>
                </li>
                <?php if (isEmployee()): ?>
                <li class="nav-item">
                    <a href="employe.php" class="nav-link">Employé</a>
                </li>
                <?php
                    endif;
                    if (isAdmin()): ?>
                <li class="nav-item">
                    <a href="admin.php" class="nav-link">Administrateur</a>
                </li>
                <?php endif; ?>
                <li class="nav-item nav-profile">
                    <div class="profile-container">
                        <div class="profile-button" id="profile-button">
                            <?php if (isLoggedIn() && !empty($_SESSION['photo'])): ?>
                                <img 
                                    src="<?= htmlspecialchars($_SESSION['photo']) ?>"
                                    alt="Photo de profil"
                                >
                            <?php else: ?>
                                <img 
                                    src="images/profile-0.jpg"
                                    alt="Photo de profil"
                                >
                            <?php endif; ?>
                        </div>
                        <div class="profile-menu" id="profile-menu">
                            <?php if (isLoggedIn()): ?>
                                <a href="compte.php">
                                    Mon compte
                                </a>
                                <a href="assets/logout.php">
                                    Se déconnecter
                                </a>
                            <?php else: ?>
                                <a href="assets/login.php">
                                    Se connecter
                                </a>
                                <a href="register.php">
                                    Créer un compte
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="toggle-menu" id="toggle-menu">
                <i class="fa-solid fa-bars-staggered"></i>
            </div>
        </nav>
    </div>
</header>