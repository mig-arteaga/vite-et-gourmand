<!-- Order -->
 <div class="order">
    <div class="order-sub-section order-info">
        <div class="order-dataset">
            <h4>Informations du client</h4>
            <div class="order-field">
                <label for="name">Nom complet :</label>
                <input type="text" name="name" id="name">
            </div>
            <div class="order-field">
                <label for="email">Email :</label>
                <input type="text" name="email" id="email">
            </div>
            <div class="order-field">
                <label for="phone">Téléphone :</label>
                <input type="tel" name="phone" id="phone">
            </div>
        </div>
        <div class="order-dataset">
            <h4>Informations de la prestation</h4>
            <div class="order-field">
                <label for="address">Adresse :</label>
                <input type="text" name="address" id="address">
            </div>
            <div class="order-field" id="address-alert">
                Votre adresse est en dehhors de Grenoble, une majoration de 59 cent. / km sera appliquée.
            </div>
            <div class="order-field">
                <label for="date">Adresse :</label>
                <input type="date" name="date" id="date">
            </div>
            <div class="order-field">
                <label for="hour">Adresse :</label>
                <input type="time" name="hour" id="hour">
            </div>
        </div>
    </div>
    <div class="order-sub-section menu-info">
        Menu (déjà choisi + liste déroulante)
        Infos du menu : prix, nb. personnes, jours min., stock
        Nb. personnes (input)
    </div>
    <div class="order-sub-section order-recap">
        Prix menu x Nb. personnes (si + 5 pers. au délà du min. 10% réduction)
        Prix de livraison (+ majoration)
        Prix total

        Commander
    </div>
 </div>