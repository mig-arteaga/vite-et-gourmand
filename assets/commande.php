<!-- Order -->
 <div class="order">
    <div class="order-sub-section order-info">
        <div class="order-dataset">
            <h4>Informations du client</h4>
            <div class="order-field">
                <label for="name" class="form-label">Nom complet :</label>
                <input type="text" name="name" class="input-element" id="name" disabled>
            </div>
            <div class="order-field">
                <label for="email" class="form-label">Email :</label>
                <input type="text" name="email" class="input-element" id="email" disabled>
            </div>
            <div class="order-field">
                <label for="phone" class="form-label">Téléphone :</label>
                <input type="tel" name="phone" class="input-element" id="phone" disabled>
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
                    <option value="">Choisir un menu</option>
                </select>
            </div>
            <div class="order-field menu-preview">
                <img src="images/menu-1-1.jpg" alt="" class="order-menu-img">
                <div>
                    <h3>Nom menu</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Culpa vel accusantium deleniti unde incidunt explicabo.</p>
                </div>
                <div class="menu-separator"></div>
                <div>
                    Hi
                </div>
            </div>
        </div>
        Menu (déjà choisi + liste déroulante) <br>
        Infos du menu : prix, nb. personnes, jours min., stock <br>
        Nb. personnes (input)
    </div>
    <div class="order-sub-section order-recap">
        Prix menu x Nb. personnes (si + 5 pers. au délà du min. 10% réduction) <br>
        Prix de livraison (+ majoration) <br>
        Prix total

        Commander
    </div>
 </div>