<?php

function getCommandeMenu($pdo) {
    // Menu
    $sql = 
        "SELECT 
            m.id_menu,
            m.titre as title,
            m.min_personnes as min_people,
            m.prix_personne as unit_price,
            m.jours_avant as delay,
            m.stock as stock,
            MAX(pm.chemin) as photo
        FROM menus m
        INNER JOIN photos_menu pm ON m.id_menu = pm.menu AND pm.ordre = 1
        WHERE id_menu = ?
    ";

    return $pdo->prepare($sql);
}

function getCommandeDishes($pdo) {
    // Dishes
    $sql = 
        "SELECT
            mp.plat as dish_number,
            p.titre as title,
            p.type as type
        FROM menus_plats mp
        INNER JOIN plats p ON mp.plat = p.id_plat
        WHERE menu = ?
    ";

    return $pdo->prepare($sql);
}

function getCommandeUser($pdo) {
    // User
    $sql = 
        "SELECT 
            id_utilisateur,
            CONCAT(prenom, ' ', nom) as full_name,
            email,
            telephone
        FROM utilisateurs
        WHERE id_utilisateur = ?
    ";

    return $pdo->prepare($sql);
}

function getCommandeMenuList($pdo) {
    // Menu list
    $sql = 
        "SELECT 
            id_menu,
            titre as title
        FROM menus
    ";

    return $pdo->prepare($sql);
}
?>