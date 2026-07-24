<?php
require_once "assets/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Accueil - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php
        require "assets/header.php";
    ?>

    <!-- Main section -->
    <section class="main-section">
        <img src="images/hero.jpg" alt="" class="parallax">
        <div class="container">
            <h2 class="sub-headline main-sub-headline">
                <span class="first-letter">B</span>ienvenue
            </h2>
            <div class="main-logo"></div>
            <!-- <img src="images/vg-logo-3.png" alt="" class="main-logo"> -->
            <div class="headline-description">
                <div class="separator">
                    <div class="line left-line"></div>
                    <div class="asterisk"><i class="fas fa-asterisk"></i></div>
                    <div class="line right-line"></div>
                </div>
                <div class="single-animation">
                    <a href="menus.php" class="btn main-btn">Voir menus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Team section -->
    <section class="team-section">
        <div class="container">
            <div class="team-info">
                <div class="team-description animate-right">
                    <div class="team-headline">
                        <h2 class="sub-headline">
                            <span class="first-letter">N</span>otre
                        </h2>
                        <h1 class="headline">Équipe</h1>
                    </div>
                    <div class="asterisk"><i class="fas fa-asterisk"></i></div>
                    <p>Derrière Vite & Gourmand se trouve une équipe passionnée par la cuisine et le partage. Chaque membre contribue au savoir-faire du restaurant, de la préparation des plats à l'accueil des clients, afin de proposer une expérience conviviale et de qualité.</p>
                    <a href="nous.php" class="btn body-btn btn-underline">Sur nous</a>
                </div>
                <div class="restaurant-info-img animate-left">
                    <img src="images/team-1.jpg" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- Between 1 -->
    <section class="tasteful-recipes between">
    </section>

    <!-- Review section -->
    <section class="review-section">
        <div class="container">
            <h2 class="sub-headline animate-bottom">
                <span class="first-letter">A</span>vis
            </h2>
            <h1 class="headline animate-bottom">Clients</h1>

            <!-- Reviews -->
            <div class="review-wrap animate-top">
            
            <!-- Gather reviews -->
            <?php
                try {
                    require_once "config/database.php";
                    
                    $sql = "SELECT 
                        a.date_avis as review_date,
                        a.note as score,
                        a.message,
                        u.nom as surname,
                        u.prenom as name,
                        u.photo
                    FROM avis a
                    INNER JOIN utilisateurs u ON a.utilisateur = u.id_utilisateur
                    WHERE a.statut = 'Validé';";
                    
                    foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $review) {
                        include "assets/review.php";
                    }
                } catch (PDOException $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
            ?>
             </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script src="js/script.js"></script>
    <!-- <script src="https://unpkg.com/scrollreveal"></script> -->
</body>
</html>