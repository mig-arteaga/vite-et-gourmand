<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuId = $_POST['menuId'];
    
    try {
        require_once "../config/database.php";
        require "commande-query.php";
    
        // Menu
        $stmt = getCommandeMenu($pdo);
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Dishes
        $stmt = getCommandeDishes($pdo);
        $stmt->execute([$menuId]);
        $dishes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Assemble all
        $detail = [
            "menu" => $menu,
            "dishes" => $dishes
        ];
    
        echo json_encode($detail);
    } catch (PDOException $e) {
        echo "SQL Error: ";
        echo $e->getMessage();
    }
}
?>