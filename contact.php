<?php
require_once "assets/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact - Vite & Gourmand</title>
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
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">N</span>ous
            </h2>
            <h1 class="headline title-up">Contacter</h1>
            <p class="p-margin">Une question, une demande d'information ou une envie de réserver une table ? L'équipe de Vite & Gourmand reste disponible pour répondre à vos demandes. Retrouvez toutes nos informations pratiques ci-dessous et n'hésitez pas à nous contacter pour préparer votre prochaine visite.</p>
            <p class="p-margin"><strong>Téléphone : </strong>+33 1 23 45 67 89</p>
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d844.0219956220348!2d5.731085646359987!3d45.19234292374179!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478af48a93b77c33%3A0x93bf5478697b83f5!2s3%20Place%20Notre%20Dame%2C%2038000%20Grenoble!5e0!3m2!1sfr!2sfr!4v1784583131775!5m2!1sfr!2sfr"
                width="100%"
                height="400"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
                class="map animate-top">
            </iframe>
        </div>
    </section>

    <!-- Between 2 -->
    <section class="tasteful-recipes between">
    </section>

    <!-- Contact -->
    <section class="contact">
        <div class="container">
            <div class="form-wrap">
                <div class="contact-info animate-right">
                    <h2 class="sub-headline">
                        <span class="first-letter">D</span>ites-nous
                    </h2>
                    <h1 class="headline">Tout</h1>
                    <p class="p-margin">Vous souhaitez nous laisser un message, partager une remarque ou obtenir des renseignements supplémentaires ? Utilisez notre formulaire de contact et notre équipe vous répondra dans les meilleurs délais.</p>
                </div>
                <div class="form-box animate-left">
                    <form class="contact-form" action="assets/contact-form.php" method="post">
                        <div class="form-content">
                            <h5 class="content-sub-title">Nom</h5>
                                <input type="text" name="name" class="nom" autocomplete="name">
                            <h5 class="content-sub-title">Adresse mail</h5>
                                <input type="text" name="mail" class="email" autocomplete="email">
                            <h5 class="content-sub-title">Sujet</h5>
                                <input type="text" name="subject" class="sujet" autocomplete="none">
                            <h5 class="content-sub-title">Message</h5>
                                <textarea name="message" class="message" name="message"></textarea>
                            <button type="submit" name="submit" class="btn body-btn btn-underline">Envoyer</a>
                        </div>
                    </form>
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