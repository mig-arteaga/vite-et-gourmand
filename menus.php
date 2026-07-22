<?php
require_once "assets/session.php";
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
            <h2 class="sub-headline">
                <span class="first-letter">T</span>ous les
            </h2>
            <h1 class="headline">Menus</h1>

            <div class="filters">
                <h4>Filtres</h4>
            </div>

            <!-- Gather menus -->
            <?php
                try {
                    require "config/database.php";
                    
                    $sql = "SELECT 
                        m.id_menu,
                        m.titre as title,
                        m.description,
                        m.min_personnes as min_people,
                        m.prix_personne as unit_price,
                        t.libelle as theme,
                        r.libelle as diet,
                        MAX(pm.chemin) as photo,
                        COUNT(DISTINCT IF(p.type = 'Entrée', p.id_plat, NULL)) as appetizer,
                        COUNT(DISTINCT IF(p.type = 'Plat', p.id_plat, NULL)) as main_course,
                        COUNT(DISTINCT IF(p.type = 'Dessert', p.id_plat, NULL)) as dessert,
                        COUNT(DISTINCT pa.allergene) as allergenic
                    FROM menus m
                    INNER JOIN themes t ON m.theme = t.id_theme
                    INNER JOIN regimes r ON m.regime = r.id_regime
                    INNER JOIN photos_menu pm ON m.id_menu = pm.menu AND pm.ordre = 1
                    INNER JOIN menus_plats mp ON m.id_menu = mp.menu
                    INNER JOIN plats p ON mp.plat = p.id_plat
                    INNER JOIN plats_allergenes pa ON p.id_plat = pa.plat
                    GROUP BY m.id_menu;";
                    
                    foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $menu) {
                        include "assets/menu.php";
                    }
                } catch (PDOException $e) {
                    echo 'Erreur : ' . $e->getMessage();
                }
            ?>

            <!-- Detailed menu -->
            <?php
                include "assets/detail-menu.php";
            ?>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>
    
    <script src="js/script.js"></script>
</body>
</html>