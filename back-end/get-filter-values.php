<?php
    try {
        require '../config/database.php';

        // Prices
        $sql = 
            "SELECT
                MIN(prix_personne) AS minPrice,
                MAX(prix_personne) AS maxPrice
            FROM menus
        ";
        $stmt = $pdo->query($sql);
        $prices = $stmt->fetch(PDO::FETCH_ASSOC);

        // Theme
        $sql = 
            "SELECT libelle AS theme
            FROM themes
        ";
        $stmt = $pdo->query($sql);
        $themes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Diet
        $sql = 
            "SELECT libelle AS diet
            FROM regimes
        ";
        $stmt = $pdo->query($sql);
        $diets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // People
        $sql = 
            "SELECT
                MIN(min_personnes) AS minPeople,
                MAX(min_personnes) AS maxPeople
            FROM menus
        ";
        $stmt = $pdo->query($sql);
        $people = $stmt->fetch(PDO::FETCH_ASSOC);

        $result = [
            'prices' => $prices,
            'themes' => $themes,
            'diets' => $diets,
            'people' => $people
        ];

        echo json_encode($result);
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
?>