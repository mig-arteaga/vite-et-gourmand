<?php
    require_once "back-end/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Menus - Vite & Gourmand</title>
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

    <!-- Menu section -->
    <section class="menu-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">T</span>ous les
            </h2>
            <h1 class="headline title-up">Menus</h1>

            <div class="filter-container">
                <h4>Filtres</h4>
                <div class="filter-wrap">
                    <div class="filter">
                        <label for="min-price">Prix :</label>
                        <div class="slider-container">
                            <div class="slider-track" id="slider-track-price">
                                <input type="range" class="slider" id="min-price">
                                <input type="range" class="slider" id="max-price">
                            </div>
                        </div>
                        <span>
                            <span id="min-price-output"></span> - 
                            <span id="max-price-output"></span>
                        </span>
                    </div>
                    <div class="filter">
                        <label for="people">Min. personnes :</label>
                        <div class="slider-container">
                            <div class="slider-track" id="slider-track-people">
                                <input type="range" class="slider" id="people">
                            </div>
                        </div>
                        <span id="people-output"></span>
                    </div>
                    <div class="filter">
                        <label for="theme">Thème :</label>
                        <select name="theme" id="theme"></select>
                    </div>
                    <div class="filter">
                        <label for="diet">Régime :</label>
                        <select name="diet" id="diet"></select>
                    </div>
                </div>
                <div>
                    <a 
                        class="btn body-btn no-link-btn filter-btn"
                        id="reset-filter-btn"
                        title="Réinitialiser">
                        <i class="fa-solid fa-filter-circle-xmark"></i>
                    </a>
                </div>
            </div>

            <div class="menu-wrap" id="menu-wrap">
                <!-- Loads menus -->
                <?php
                    require "back-end/load-menus.php"
                ?>
            </div>

            <!-- Detailed menu -->
            <?php
                include "back-end/load-detail-menu.php";
            ?>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>
    
    <script type="module" src="js/main.js"></script>
</body>
</html>