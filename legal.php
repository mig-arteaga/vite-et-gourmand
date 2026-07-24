<?php
require_once "assets/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mentions légales - Vite & Gourmand</title>
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

    <!-- Contact section -->
    <section class="contact-section first-section">
        <div class="container legal-container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">M</span>entions
            </h2>
            <h1 class="headline title-up">Légales</h1>
            <h3>Éditeur du site</h3>
                <p>
                    Le site <strong>Vite & Gourmand</strong> est édité par l'établissement Vite & Gourmand.
                </p>
                <p>
                    Adresse : 3 Place Notre Dame, 38000 Grenoble<br>
                    Téléphone : +33 1 23 45 67 89<br>
                    Email : admin@vitegourmand.com
                </p>
                <p>
                    Le responsable de la publication est le représentant de l'établissement Vite & Gourmand.
                </p>

                <h3>Hébergement</h3>
                <p>
                    Le site est hébergé par :
                </p>
                <p>
                    <strong>Heroku</strong><br>
                    Heroku, Inc.<br>
                    650 7th Street, San Francisco, CA 94103, États-Unis
                </p>
                <p>
                    La base de données de l'application est hébergée par :
                </p>
                <p>
                    <strong>Railway</strong><br>
                    Railway Corp.
                </p>

                <h3>Protection des données</h3>
                <p>
                    Les informations collectées via les formulaires du site sont utilisées uniquement
                    dans le cadre du traitement des demandes des utilisateurs.
                </p>
                <p>
                    Conformément au Règlement Général sur la Protection des Données (RGPD),
                    chaque utilisateur dispose d'un droit d'accès, de modification et de suppression
                    de ses données personnelles en contactant l'établissement.
                </p>

                <h3>Propriété intellectuelle</h3>
                <p>
                    L'ensemble des éléments présents sur le site Vite & Gourmand, notamment les textes,
                    images, logos et éléments graphiques, sont protégés par les règles relatives
                    à la propriété intellectuelle.
                </p>
                <p>
                    Toute reproduction ou utilisation sans autorisation préalable est interdite.
                </p>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script src="js/script.js"></script>
</body>
</html>