<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $menuId = $_POST['menu'];

    try {
        require_once "../config/database.php";

        // Menu
        $sql = 
            "SELECT 
                m.id_menu,
                m.titre as title,
                m.description as description,
                m.min_personnes as min_people,
                m.prix_personne as unit_price,
                m.jours_avant as delay,
                m.stock as stock,
                t.libelle as theme,
                r.libelle as diet
            FROM menus m
            INNER JOIN themes t ON m.theme = t.id_theme
            INNER JOIN regimes r ON m.regime = r.id_regime
            WHERE id_menu = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);

        // Photos
        $sql = 
            "SELECT chemin as path
            FROM photos_menu
            WHERE menu = ?
            ORDER BY ordre
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$menuId]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        // Dishes
        $sql = 
            "SELECT
                a.libelle as allergenic
            FROM menus_plats mp
            INNER JOIN plats p ON mp.plat = p.id_plat
            INNER JOIN plats_allergenes pa ON p.id_plat = pa.plat
            INNER JOIN allergenes a ON pa.allergene = a.id_allergene
            WHERE menu = ?
            GROUP BY a.libelle
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$menuId]);
        $allergenics = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assemble all
        $detail = [
            "menu" => $menu,
            "photos" => $photos,
            "dishes" => $dishes,
            "allergenics" => $allergenics
        ];

        echo json_encode($detail);
    } catch (PDOException $e) {
        echo "SQL Error: ";
        echo $e->getMessage();
    }

    exit;
};

include "assets/detail-menu.php";
?>