<?php
require_once "assets/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nous - Vite & Gourmand</title>
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

    <!-- Nous section -->
    <section class="nous-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">Q</span>ui
            </h2>
            <h1 class="headline title-up">Sommes-nous</h1>

            <div class="team-slide">
                <img src="images/team-1.jpg" alt="" class="team-slide-image">
                <img src="images/team-2.jpg" alt="" class="team-slide-image">
                <img src="images/team-3.jpg" alt="" class="team-slide-image">
                <img src="images/team-4.jpg" alt="" class="team-slide-image">
                <img src="images/team-5.jpg" alt="" class="team-slide-image">
                <img src="images/team-6.jpg" alt="" class="team-slide-image">
                <img src="images/team-7.jpg" alt="" class="team-slide-image">
                <img src="images/team-1.jpg" alt="" class="team-slide-image">
                <img src="images/team-2.jpg" alt="" class="team-slide-image">
                <img src="images/team-3.jpg" alt="" class="team-slide-image">
                <img src="images/team-4.jpg" alt="" class="team-slide-image">
                <img src="images/team-5.jpg" alt="" class="team-slide-image">
                <img src="images/team-6.jpg" alt="" class="team-slide-image">
                <img src="images/team-7.jpg" alt="" class="team-slide-image">
            </div>
            <p class="p-margin">Vite & Gourmand est un restaurant qui place la qualité, la convivialité et le plaisir de partager au cœur de son activité. Notre équipe est composée de passionnés de cuisine et de service, réunis autour d'une même volonté : proposer une expérience agréable à chaque client.</p>
            <p class="p-margin">Nous travaillons chaque jour à sélectionner des produits de qualité afin de préparer des plats savoureux, équilibrés et accessibles. De la conception des menus à l'accueil en salle, chaque membre de l'équipe contribue à créer une atmosphère chaleureuse et authentique.</p>
            <p class="p-margin">Situé à Grenoble, Vite & Gourmand souhaite offrir un lieu où chacun peut prendre le temps de découvrir une cuisine faite avec attention, dans un cadre accueillant. Notre engagement repose sur la satisfaction de nos clients, le respect des produits et le plaisir de partager un bon moment autour d'un repas.</p>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>