<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $maxPrice = (int)$_POST['maxPrice'];
    $theme = $_POST['theme'];
    $diet = $_POST['diet'];

    try {
        require_once "../config/database.php";
        require "../back-end/menu-query.php";

        $filters = [];

        if (!empty($maxPrice)) {
            $filters[':price'] = $maxPrice;
        }
        if (!empty($theme)) {
            $filters[':theme'] = $theme;
        }
        if (!empty($diet)) {
            $filters[':diet'] = $diet;
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