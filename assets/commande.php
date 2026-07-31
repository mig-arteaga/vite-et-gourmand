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
                    <label for="city" class="form-label">Ville :</label>
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
                    <input type="date" name="date" class="input-element" id="date">
                </div>
                <div class="order-field">
                    <label for="hour" class="form-label">Heure :</label>
                    <input type="time" name="hour" class="input-element" id="hour">
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
                    <?php foreach ($menuList as $menu): ?>
                        <option 
                            value="<?= $menu['id_menu'] ?>"
                            <?= $menu['id_menu'] == $menuId ? 'selected' : '' ?>
                            >
                            <?= $menu['title'] ?>
                            </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="order-field menu-preview">
                <img src="images/menu-1-1.jpg" alt="" class="order-menu-img">
                <div>
                    <h3>Nom menu</h3>
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bread-slice color-1"></i>
                            <span id="detail-appetizer">Entrée</span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-bowl-food color-2"></i>
                            <span id="detail-main-course">Plat</span>
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-cookie-bite color-5"></i>
                            <span id="detail-dessert">Dessert</span>
                        </li>
                    </ul>
                </div>
                <div class="menu-separator"></div>
                <div class="menu-preview-plus">
                    <ul class="menu-list">
                        <li class="menu-list-item">
                            <i class="fa-solid fa-people-group color-3"></i>
                            <span id="detail-min-people"></span> personnes min.
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-euro-sign color-5"></i>
                            <span id="detail-unit-price"></span>€ / personne
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-calendar color-2"></i>
                            <span id="detail-delay"></span> jours de délai min.
                        </li>
                        <li class="menu-list-item">
                            <i class="fa-solid fa-warehouse color-4"></i>
                            <span id="detail-stock"></span> menus disponibles
                        </li>
                    </ul>
                </div>
            </div>
            <div class="order-field people-field">
                <label for="people" class="form-label">Nb personnes : <span class="required">*</span></label>
                <input 
                    type="number" 
                    name="people" 
                    class="input-element" 
                    id="people"
                    min="5"
                    max="10"
                    value="5"
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
                    <p>150€</p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label">Réduction grand groupe (-10%) :</span>
                    <p>-15€</p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label">Frais de livraison :</span>
                    <p>5€</p>
                </div>
                <div class="order-field order-right">
                    <span class="form-label">Frais kilométriques :</span>
                    <p>15.20€</p>
                </div>
            </div>
            <div class="order-field order-right">
                <span class="form-label"><strong>Prix total :</strong></span>
                <p><strong>155.20€</strong></p>
            </div>
            <a class="btn body-btn btn-underline no-link-btn">Commander</a>
        </div>
    </div>
 </div>