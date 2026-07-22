<!-- Menu -->
<div class="menu">
    <img src="<?= htmlspecialchars($menu['photo']); ?>" alt="" class="menu-img">
    <div class="menu-grid">
        <div class="menu-info">
            <h3>
                <?php echo htmlspecialchars($menu['title']); ?>
            </h3>
            <p>
                <?php echo htmlspecialchars($menu['description']); ?>
            </p>
        </div>
        <div class="menu-separator"></div>
        <div class="menu-plus">
            <ul class="menu-list">
                <li class="menu-list-item">
                    <i class="fa-solid fa-champagne-glasses color-1"></i>
                    <?php echo htmlspecialchars($menu['theme']); ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-wheat-awn-circle-exclamation color-2"></i>
                    <?php echo htmlspecialchars($menu['diet']); ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-bowl-food color-3"></i>
                    <?php 
                        $appetizer = (int)htmlspecialchars($menu['appetizer']);
                        $mainCourse = (int)htmlspecialchars($menu['main_course']);
                        $dessert = (int)htmlspecialchars($menu['dessert']);
                        $courses = $appetizer + $mainCourse + $dessert;
                        
                        if ($courses == 3) {
                            echo 'Entrée + plat + dessert';
                        }
                        else if ($appetizer == 1) {
                            echo 'Entrée + plat';
                            }
                        else {
                            echo 'Plat + dessert';
                        };
                    ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-shrimp color-4"></i>
                    <?php echo htmlspecialchars($menu['allergenic']).' allergènes' ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-people-group color-5"></i>
                    <?php echo htmlspecialchars($menu['min_people']).' personnes min.'; ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-euro-sign color-2"></i>
                    <?php 
                        $price = htmlspecialchars($menu['unit_price']);
                        if (($price - (int)$price) == 0) {
                            echo (int)$price;
                        }
                        else {
                            echo $price;
                        };

                        echo '€ / personne';

                    ?>
                </li>
            </ul>
            <a 
                class="btn body-btn btn-underline menu-btn no-link-btn"
                id="<?=  $menu['id_menu'] ?>">Voir plus</a>
        </div>
    </div>
</div>