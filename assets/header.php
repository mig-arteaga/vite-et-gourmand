<?php
require_once "session.php";
?>

<!-- Header -->
<header class="header-2">
    <div class="container">
        <nav class="nav">
            <a href="index.php">
                <img src="images/vg-logo-2.png" alt="" class="title-img">
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
                <li class="nav-item">
                    <a href="assets/logout.php" class="login">
                        <?php if (isset($_SESSION['photo']) && !empty($_SESSION['photo'])) { ?>
                            <img 
                                src="<?= htmlspecialchars($_SESSION['photo']) ?>"
                                alt="Profile photo"
                            >
                        <?php } else { ?>
                            <img 
                                src="images/profile-0.jpg"
                                alt="Profile photo"
                            >
                        <?php } ?>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>