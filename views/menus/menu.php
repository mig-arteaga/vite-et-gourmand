<!-- Menu -->
<div class="menu">
    <img src="<?= htmlspecialchars($menu->getPhotos()[0]->getPath()); ?>" alt="" class="menu-img">
    <div class="menu-grid">
        <div class="menu-info">
            <h3>
                <?php echo htmlspecialchars($menu->getTitle()); ?>
            </h3>
            <p>
                <?php echo htmlspecialchars($menu->getDescription()); ?>
            </p>
        </div>
        <div class="menu-separator"></div>
        <div class="menu-plus">
            <ul class="menu-list">
                <li class="menu-list-item">
                    <i class="fa-solid fa-champagne-glasses color-1"></i>
                    <?php echo htmlspecialchars($menu->getTheme()->getLabel()); ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-wheat-awn-circle-exclamation color-2"></i>
                    <?php echo htmlspecialchars($menu->getRegime()->getLabel()); ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-bowl-food color-3"></i>
                    <?php echo htmlspecialchars($menuService->getCourseDescription($menu)); ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-shrimp color-4"></i>
                    <?php echo $menuService->getAllergenCount($menu) . ' allergènes'; ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-people-group color-5"></i>
                    <?php echo $menu->getMinPeople() . ' personnes min.'; ?>
                </li>
                <li class="menu-list-item">
                    <i class="fa-solid fa-euro-sign color-2"></i>
                    <?php
                        $price = $menu->getUnitPrice();

                        if ($price === (float) (int) $price) {
                            echo (int) $price;
                        }
                        else {
                            echo $price;
                        }

                        echo '€ / personne';
                    ?>
                </li>
            </ul>
            <a
                class="btn body-btn btn-underline menu-btn no-link-btn"
                id="<?= $menu->getId() ?>">
                Voir plus
            </a>
        </div>
    </div>
</div>