<?php
    \App\Core\Session::start();
?>

<!-- Header -->
<header id="header">
    <div class="container">
        <nav class="nav">
            <a href="/" class="logo-link">
                <img src="images/vg-logo-1.svg" alt="" class="title-img">
            </a>
            <ul class="nav-list" id="nav-list">
                <li class="nav-item">
                    <a href="/" class="nav-link">Accueil</a>
                </li>
                <li class="nav-item">
                    <a href="/menus" class="nav-link">Menus</a>
                </li>
                <li class="nav-item">
                    <a href="/nous" class="nav-link">Nous</a>
                </li>
                <li class="nav-item">
                    <a href="/contact" class="nav-link">Contact</a>
                </li>
                <?php if (\App\Core\Session::isEmployee()): ?>
                <li class="nav-item">
                    <a href="/employe" class="nav-link">Employé</a>
                </li>
                <?php
                    endif;
                    if (\App\Core\Session::isAdmin()): ?>
                <li class="nav-item">
                    <a href="/admin" class="nav-link">Admin</a>
                </li>
                <?php endif; ?>
                <li class="nav-item nav-profile">
                    <div class="profile-container">
                        <div class="profile-button" id="profile-button">
                            <?php if (\App\Core\Session::isLoggedIn() && !empty($_SESSION['photo'])): ?>
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
                            <?php if (\App\Core\Session::isLoggedIn()): ?>
                                <a href="/compte">
                                    Mon compte
                                </a>
                                <a href="/logout">
                                    Se déconnecter
                                </a>
                            <?php else: ?>
                                <a href="/login">
                                    Se connecter
                                </a>
                                <a href="">
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