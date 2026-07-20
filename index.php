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
    <section class="menu-section" id="main-section">
        <!-- <img src="images/hero.jpg" alt="" class="parallax"> -->
        <div class="container">
            <h2 class="sub-headline">
                <span class="first-letter">T</span>ous les
            </h2>
            <h1 class="headline headline-dark">Menus</h1>

            <!-- Menu -->
            <?php
                require "assets/menu.php";
            ?>
        </div>
    </section>

    <!-- Between 1 -->
    <section class="tasteful-recipes between">
    </section>

    <!-- Avis clients -->
    <section>
        <div class="container">
            <h2 class="sub-headline">
                <span class="first-letter">A</span>vis
            </h2>
            <h1 class="headline headline-dark">Clients</h1>

            <!-- Avis -->
            <?php
                require "assets/review.php";
            ?>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>
</body>
</html>