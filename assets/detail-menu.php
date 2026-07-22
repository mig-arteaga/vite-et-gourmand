<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $menuId = $_POST['menu'];

    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=vite_et_gourmand',
            'root',
            ''
        );

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
}
?>

<div class="detail-bg" id="detail-bg">
    <div class="detail-menu container" id="detail-menu">
        <div class="close-btn">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <div class="menu-slide" id="menu-slide">
        </div>
        <div class="detail-menu-grid">
            <div class="detail-menu-info">
                <div>
                    <h3 id="detail-title"></h3>
                    <p id="detail-description"></p>
                </div>
                <div>
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-champagne-glasses color-1"></i>
                            <span id="detail-theme"></span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-wheat-awn-circle-exclamation color-2"></i>
                            <span id="detail-diet"></span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bowl-food color-3"></i>
                            <span id="detail-composition"></span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="menu-separator"></div>
            <div class="detail-menu-plus">
                <div class="menu-composition">
                    <h4>Composition</h4>
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bread-slice color-1"></i>
                            <span id="detail-appetizer"></span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bowl-food color-2"></i>
                            <span id="detail-main-course"></span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-cookie-bite color-5"></i>
                            <span id="detail-dessert"></span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-shrimp color-4"></i>
                            <strong class="blue">Liste d'allergènes :</strong>
                            <ul class="sub-list" id="allergenic-list"></ul>
                        </li>
                    </ul>
                </div>
                <div class="menu-conditions">
                    <h4>Conditions</h4>
                    <ul class="menu-list r-side-list">
                        <li class="menu-list-item">
                            <span id="detail-min-people"></span> personnes min.
                            <i class="fa-solid fa-people-group color-3"></i>
                        </li>
                        <li class="menu-list-item">
                            <span id="detail-unit-price"></span>€ / personne
                            <i class="fa-solid fa-euro-sign color-5"></i>
                        </li>
                        <li class="menu-list-item">
                            <span id="detail-delay"></span> jours de délai min.
                            <i class="fa-solid fa-calendar color-2"></i>
                        </li>
                        <li class="menu-list-item">
                            <span id="detail-stock"></span> menus disponibles
                            <i class="fa-solid fa-warehouse color-4"></i>
                        </li>
                    </ul>
                    <a href="#" class="btn body-btn btn-underline no-link-btn">Commander</a>
                </div>
            </div>
        </div>
    </div>
</div>