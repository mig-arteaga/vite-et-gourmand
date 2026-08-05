<?php
    require "back-end/auth.php";
    requireRoles(["Administrateur", "Employé", "Utilisateur"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Commande - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <?php
        require "assets/header.php";
    ?>

    <!-- Order section -->
    <section class="order-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">C</span>ommandez
            </h2>
            <h1 class="headline title-up">Un menu</h1>

            <div class="order-container">
                <?php 
                    require "back-end/load-commande.php"
                ?>
            </div>
        </div>
        <div class="confirmation-bg" id="confirmation-bg">
            <div class="container">
                <div class="confirmation-box">
                    <div class="close-btn" id="close-recap-btn">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                    <div class="order-dataset">
                        <h4>Récapitulatif de la commande</h4>
                        <div class="sub-dataset">
                            <div class="order-field order-right">
                                <span class="form-label">Addresse :</span>
                                <p><span id="recap-address">Test</span></p>
                            </div>
                            <!-- <div class="order-field order-right">
                                <span class="form-label">Ville :</span>
                                <p><span id="menu-price">Test</span></p>
                            </div>
                            <div class="order-field order-right">
                                <span class="form-label">Code postal :</span>
                                <p><span id="menu-price">Test</span></p>
                            </div> -->
                            <div class="order-field order-right">
                                <span class="form-label">Date et heure :</span>
                                <p><span id="recap-datetime">Test</span></p>
                            </div>
                            <!-- <div class="order-field order-right">
                                <span class="form-label">Heure :</span>
                                <p><span id="menu-price">Test</span></p>
                            </div> -->
                            <div class="order-field order-right">
                                <span class="form-label">Menu :</span>
                                <p><span id="recap-menu">Test</span></p>
                            </div>
                            <div class="order-field order-right">
                                <span class="form-label">Nb. personnes :</span>
                                <p><span id="recap-people">Test</span></p>
                            </div>
                        </div>
                        <div class="order-field order-right total-line">
                            <span class="form-label strong">Prix total :</span>
                            <p class="strong">
                                <span id="recap-total">Test</span>
                                <span>€</span>
                            </p>
                        </div>
                        <div class="order-field order-right order-btn-wrap">
                            <a class="btn body-btn btn-underline no-link-btn" id="return-btn">Retour</a>
                            <a class="btn body-btn btn-underline no-link-btn" id="order-confirm-btn">Commander</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>
    
    <script type="module" src="js/main.js"></script>
</body>
</html>