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
            <h2 class="sub-headline">
                <span class="first-letter">B</span>ienvenue
            </h2>
            <img src="images/vg-logo-3.png" alt="" class="main-logo">
            <div class="headline-description">
                <div class="separator">
                    <div class="line left-line"></div>
                    <div class="asterisk"><i class="fas fa-asterisk"></i></div>
                    <div class="line right-line"></div>
                </div>
                <div class="single-animation">
                    <a href="#" class="btn main-btn">Voir menus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Notre équipe -->
    <section class="team-section">
        <div class="container">
            <div class="team-info">
                <div class="team-description">
                    <div class="team-headline">
                        <h2 class="sub-headline">
                            <span class="first-letter">N</span>otre
                        </h2>
                        <h1 class="headline">Equipe</h1>
                    </div>
                    <div class="asterisk"><i class="fas fa-asterisk"></i></div>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Repellendus suscipit repudiandae vel alias, eius incidunt quis. Facilis, commodi iste. Modi neque ipsa maiores eligendi porro quia, nulla facilis totam ab!</p>
                    <a href="#" class="btn body-btn btn-underline">Sur nous</a>
                </div>
                <div class="restaurant-info-img animate-right">
                    <img src="images/team-1.jpg" alt="">
                </div>
            </div>
        </div>
    </section>

    <!-- Between 1 -->
    <section class="tasteful-recipes between">
    </section>

    <!-- Avis clients -->
    <section class="review-section">
        <div class="container">
            <h2 class="sub-headline">
                <span class="first-letter">A</span>vis
            </h2>
            <h1 class="headline headline-dark">Clients</h1>

            <!-- Avis -->
             <div class="review-wrap">
                <?php
                    require "assets/review.php";
                ?>
                <?php
                    require "assets/review.php";
                ?>
                <?php
                    require "assets/review.php";
                ?>
             </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script src="js/script.js"></script>
</body>
</html>