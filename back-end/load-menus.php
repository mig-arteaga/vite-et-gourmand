<?php
    try {
        require "config/database.php";
        require "back-end/menu-query.php";

        $stmt = getMenus($pdo);
        $stmt->execute();

        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($menus) === 0) {
            include "assets/no-results.php";
            exit;
        }
        
        foreach ($menus as $menu) {
            include "assets/menu.php";
        }
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
?>