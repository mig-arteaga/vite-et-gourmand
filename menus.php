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
            <h2 class="sub-headline">
                <span class="first-letter">T</span>ous les
            </h2>
            <h1 class="headline">Menus</h1>

            <div class="filters">
                <h4>Filtres</h4>
            </div>

            <!-- Menu -->
            <?php
                require "assets/menu.php";
                require "assets/menu.php";
                require "assets/menu.php";
                require "assets/menu.php";
            ?>

            <!-- Detailed menu -->
            <div class="detail-bg">
                <?php
                    require "assets/detail-menu.php";
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