<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $minPrice = (int)$_POST['minPrice'];
    $maxPrice = (int)$_POST['maxPrice'];
    $theme = $_POST['theme'];
    $diet = $_POST['diet'];
    $people = (int)$_POST['people'];

    try {
        require_once "../config/database.php";
        require "../back-end/menu-query.php";

        $filters = [];

        if (!empty($minPrice)) {
            $filters[':minPrice'] = $minPrice;
        }
        if (!empty($maxPrice)) {
            $filters[':maxPrice'] = $maxPrice;
        }
        if (!empty($theme)) {
            $filters[':theme'] = $theme;
        }
        if (!empty($diet)) {
            $filters[':diet'] = $diet;
        }
        if (!empty($people)) {
            $filters[':people'] = $people;
        }

        $stmt = getMenus($pdo, $filters);
        $stmt->execute($filters);
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($menus as $menu) {
            include "../assets/menu.php";
        }
    } catch (PDOException $e) {
        echo "SQL Error: ";
        echo $e->getMessage();
    }

    exit;
};
?>