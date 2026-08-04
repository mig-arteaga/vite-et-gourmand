<!-- Order -->
 <div class="order">
    <div class="order-sub-section order-info">
        <div class="order-dataset">
            <h4>Informations du client</h4>
            <div class="order-field">
                <label for="name" class="form-label">Nom complet :</label>
                <input type="text" 
                    name="name" 
                    class="input-element" 
                    id="name" 
                    value="<?= htmlspecialchars($user['full_name']) ?>" 
                    disabled>
            </div>
            <div class="order-field">
                <label for="email" class="form-label">Email :</label>
                <input 
                    type="text" 
                    name="email" 
                    class="input-element" 
                    id="email" 
                    value="<?= htmlspecialchars($user['email']) ?>" 
                    disabled>
            </div>
            <div class="order-field">
                <label for="phone" class="form-label">Téléphone :</label>
                <input type="tel" 
                    name="phone" 
                    class="input-element" 
                    id="phone" 
                    value="<?= htmlspecialchars($user['telephone']) ?>" 
                    disabled>
            </div>
        </div>
        <div class="order-dataset">
            <h4>Informations de la prestation</h4>
            <div class="order-field">
                <label for="address" class="form-label">Adresse :</label>
                <input type="text" name="address" class="input-element" id="address">
            </div>
            <div class="order-field date-hour">
                <div class="order-field">
                    <label for="city" class="form-label">
                        Ville :
                        <i class="fa-solid fa-circle-info color-2 info-city">
                            <span class="info-city-text">
                                Des frais kilométriques seront appliqués si la livraison est en dehors de Grenoble. (0.59€/km)
                            </span>
                        </i>
                    </label>
                    <input type="text" name="city" class="input-element" id="city">
                </div>
                <div class="order-field">
                    <label for="zipcode" class="form-label">Code postal :</label>
                    <input type="text" name="zipcode" class="input-element" id="zipcode">
                </div>
            </div>
            <div class="order-field" id="address-alert">
                Votre adresse est en dehhors de Grenoble, une majoration de 59 cent. / km sera appliquée.
            </div>
            <div class="order-field date-hour">
                <div class="order-field">
                    <label for="date" class="form-label">Date :</label>
                    <?php
                        $today = New DateTime();
                        $minDate = New DateTime();
                        $maxDate = New DateTime();
                        $minDate->modify('+5 days');
                        $maxDate->modify('+6 months');
                    ?>
                    <input 
                        type="date" 
                        name="date" 
                        class="input-element" 
                        id="date" 
                        min="<?= $minDate->format('Y-m-d') ?>" 
                        max="<?= $maxDate->format('Y-m-d') ?>"
                    >
                </div>
                <div class="order-field">
                    <label for="hour" class="form-label">Heure :</label>
                    <input 
                        type="time" 
                        name="hour" 
                        class="input-element" 
                        id="hour" 
                        min="08:30" 
                        max="22:30" 
                        step="900"
                    >
                </div>
            </div>
        </div>
    </div>
    <div class="order-sub-section menu-info">
        <div class="order-dataset">
            <h4>Informations du menu</h4>
            <div class="order-field">
                <label for="menu" class="form-label">Menu :</label>
                <select name="menu" class="input-element" id="menu-list">
                    <?php foreach ($menuList as $menuListItem): ?>
                        <option 
                            value="<?= $menuListItem['id_menu'] ?>"
                            <?= $menuListItem['id_menu'] == $menuId ? 'selected' : '' ?>
                            >
                            <?= $menuListItem['title'] ?>
                            </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="order-field menu-preview">
                <img 
                    src="<?= $menu['photo'] ?>" 
                    alt="" 
                    class="order-menu-img"
                    id="order-menu-img"
                >
                <div>
                    <h3 id="menu-title"><?= $menu['title'] ?></h3>
                    <?php
                        $dishesByType = [];

                        foreach ($dishes as $dish) {
                            $dishesByType[$dish['type']] = $dish['title'];
                        }
                    ?>
                    <ul class="menu-list">
                        <li class="menu-list-item dish-list-item">
                            <i class="fa-solid fa-bread-slice color-1"></i>
                            <span id="detail-appetizer">
                                <?= isset($dishesByType['Entrée']) ? htmlspecialchars($dishesByType['Entrée']) : '' ?>
                            </span>
                        </li>
                        <li class="menu-list-item dish-list-item">
                            <i class="fa-solid fa-bowl-food color-2"></i>
                            <span id="detail-main-course">
                                <?= isset($dishesByType['Plat']) ? htmlspecialchars($dishesByType['Plat']) : '' ?>
                            </span>
                        </li>
                        <li class="menu-list-item dish-list-item">
                            <i class="fa-solid fa-cookie-bite color-5"></i>
                            <span id="detail-dessert">
                                <?= isset($dishesByType['Dessert']) ? htmlspecialchars($dishesByType['Dessert']) : '' ?>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="menu-separator"></div>
                <div class="menu-preview-plus">
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-people-group color-3"></i>
                            <span id="detail-min-people">
                                <?= $menu['min_people'] ?>
                            </span> personnes min.
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-euro-sign color-5"></i>
                            <span id="detail-unit-price">
                                <?php 
                                    $price = htmlspecialchars($menu['unit_price']);
                                    if (($price - (int)$price) == 0) {
                                        echo (int)$price;
                                    }
                                    else {
                                        echo $price;
                                    };

                                    // echo '€ / personne';
                                ?>
                            </span>€ / personne
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-calendar color-2"></i>
                            <span id="detail-delay">
                                <?= $menu['delay'] ?>
                            </span> jours de délai min.
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-warehouse color-4"></i>
                            <span id="detail-stock">
                                <?= $menu['stock'] ?>
                            </span> menus disponibles
                        </li>
                    </ul>
                </div>
            </div>
            <div class="order-field people-field">
                <label for="people" class="form-label">
                    Nb personnes : 
                    <i class="fa-solid fa-circle-info color-2 info-people">
                        <span class="info-people-text">
                            -10% si au moins 5 personnes au dessus de la quantité minimum indiquée dans le menu.
                        </span>
                    </i>
                </label>
                <input 
                    type="number" 
                    name="people" 
                    class="input-element" 
                    id="people"
                    min="<?= $menu['min_people'] ?>"
                    max="<?= $menu['stock'] ?>"
                    value="<?= $menu['min_people'] ?>"
                >
            </div>
        </div>
    </div>
    <div class="order-sub-section order-recap">
        <div class="order-dataset">
            <h4>Récapitulatif de la commande</h4>
            <div class="sub-dataset">
                <div class="order-field order-right">
                    <span class="form-label">Prix des menus :</span>
                    <p>
                        <span id="menu-price">
                            <?php 
                                $menuPrice = $menu['unit_price'] * $menu['min_people'];

                                echo $menuPrice;
                            ?>
                        </span>
                        <span>€</span>
                    </p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label">Réduction grand groupe (-10%) :</span>
                    <p>
                        <span id="group-offer">
                            -0
                        </span>
                        <span>€</span>
                    </p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label">Frais de livraison :</span>
                    <p>
                        <span id="delivery-fee">
                            5
                        </span>
                        <span>€</span>
                    </p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label"> Frais kilométriques :</span>
                    <p>
                        <span id="distance-fee">
                            0
                        </span>
                        <span>€</span>
                    </p>
                </div>
            </div>
            <div class="order-field order-right">
                <span class="form-label"><strong>Prix total :</strong></span>
                <p class="strong">
                    <span id="total-price">                        
                        <?php 
                            echo $menuPrice + 5;
                        ?>
                    </span>
                    <span>€</span>
                </p>
            </div>
            <a class="btn body-btn btn-underline no-link-btn">Commander</a>
        </div>
    </div>
 </div>