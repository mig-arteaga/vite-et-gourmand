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

            <div class="filter-wrap">
                <h4>Filtres</h4>
                <div class="filters">
                    <div class="filter">
                        <label for="max-price">Prix max. :</label>
                        <input type="text" name="max-price" id="max-price">
                    </div>
                    <div class="filter">
                        <label for="theme">Thème :</label>
                        <input type="text" name="theme" id="theme">
                    </div>
                    <div class="filter">
                        <label for="max-price">Régime :</label>
                        <input type="text" name="diet" id="diet">
                    </div>
                    <a 
                        class="btn body-btn btn-underline no-link-btn"
                        id="filter-btn">
                        Filter
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