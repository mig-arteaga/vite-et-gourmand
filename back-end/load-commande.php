<?php
$menuId = $_GET['menu'];
$userId = $_SESSION['user_id'];

try {
    require_once "config/database.php";

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
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
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

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$menuId]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

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

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$menuId]);
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Menu list
    $sql = 
        "SELECT 
            id_menu,
            titre as title
        FROM menus
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $menuList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Assemble all
    $detail = [
        "menu" => $menu,
        "dishes" => $dishes,
        "user" => $user,
        "menuList" => $menuList
    ];

    echo json_encode($detail);
} catch (PDOException $e) {
    echo "SQL Error: ";
    echo $e->getMessage();
}

include "assets/commande.php";
?>