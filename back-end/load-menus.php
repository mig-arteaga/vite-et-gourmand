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