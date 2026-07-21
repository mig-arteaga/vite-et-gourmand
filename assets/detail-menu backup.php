<?php
// echo "<script>console.log('detail-menu.php loaded in DOM')</script>";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $menuId = $_POST['menu'];

    try {
        $pdo = new PDO(
            'mysql:host=localhost;dbname=vite_et_gourmand',
            'root',
            ''
        );

        $sql = 
            "SELECT 
                titre,
                description,
                prix_personne
            FROM menus
            WHERE id_menu = ?
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$menuId]);
        $menu = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode($menu);
    } catch (PDOException $e) {
        echo "SQL Error: ";
        echo $e->getMessage();
    }

    exit;
}
?>

<!-- Detailed menu -->
 <div class="detail-bg" id="detail-bg">
    <div class="detail-menu container" id="detail-menu">
        <div class="close-btn">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <div class="menu-slide">
            <img src="images/menu-1-1.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-2.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-3.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-4.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-5.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-1.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-2.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-3.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-4.jpg" alt="" class="menu-slide-image">
            <img src="images/menu-1-5.jpg" alt="" class="menu-slide-image">
        </div>
        <div class="detail-menu-grid">
            <div class="detail-menu-info">
                <div>
                    <h3 id="detail-title"></h3>
                    <p>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quaerat debitis architecto quia culpa blanditiis. Culpa voluptatibus obcaecati, possimus suscipit iste, cupiditate assumenda autem eum eligendi sequi dignissimos eaque accusantium odio.</p>
                </div>
                <div>
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-champagne-glasses color-1"></i>
                            Anniversaire
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-wheat-awn-circle-exclamation color-2"></i>
                            Pas de régime
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bowl-food color-3"></i>
                            Entrée + plat + dessert
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
                            Nom de l'entrée proposée
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bowl-food color-2"></i>
                            Nom du plat proposé
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-cookie-bite color-5"></i>
                            Nom du dessert proposé
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-shrimp color-4"></i>
                            <strong class="blue">Liste d'allergènes :</strong>
                            <ul class="sub-list">
                                <li class="sub-list-item">Céréales</li>
                                <li class="sub-list-item">Crustacés</li>
                                <li class="sub-list-item">Poisson</li>
                                <li class="sub-list-item">Soja</li>
                                <li class="sub-list-item">Lait</li>
                                <li class="sub-list-item">Céléri</li>
                                <li class="sub-list-item">Moutarde</li>
                                <li class="sub-list-item">Noix</li>
                                <li class="sub-list-item">Lupin</li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <div class="menu-conditions">
                    <h4>Conditions</h4>
                    <ul class="menu-list r-side-list">
                        <li class="menu-list-item">
                            5 personnes min.
                            <i class="fa-solid fa-people-group color-5"></i>
                        </li>
                        <li class="menu-list-item">
                            60€ / personne
                            <i class="fa-solid fa-euro-sign color-2"></i>
                        </li>
                        <li class="menu-list-item">
                            5 jours de délai min.
                            <i class="fa-solid fa-calendar color-4"></i>
                        </li>
                    </ul>
                    <a href="#" class="btn body-btn btn-underline no-link-btn">Commander</a>
                </div>
            </div>
        </div>
    </div>
</div>