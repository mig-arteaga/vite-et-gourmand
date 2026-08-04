<?php
$menuId = $_GET['menu'];
$userId = $_SESSION['user_id'];

try {
    require_once "config/database.php";
    require "back-end/commande-query.php";

    // Menu
    $stmt = getCommandeMenu($pdo);
    $stmt->execute([$menuId]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    // Dishes
    $stmt = getCommandeDishes($pdo);
    $stmt->execute([$menuId]);
    $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // User
    $stmt = getCommandeUser($pdo);
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Menu list
    $stmt = getCommandeMenuList($pdo);
    $stmt->execute();
    $menuList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Assemble all
    $detail = [
        "menu" => $menu,
        "dishes" => $dishes,
        "user" => $user,
        "menuList" => $menuList
    ];

    // echo json_encode($detail);
} catch (PDOException $e) {
    echo "SQL Error: ";
    echo $e->getMessage();
}

include "assets/commande.php";
?>