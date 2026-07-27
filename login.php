<?php
require_once "back-end/session.php";
require "back-end/load-login.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Connexion - Vite & Gourmand</title>
    <!--Font awesome CDN-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.0/css/all.min.css">
    <!--Scroll reveal CDN-->
    <script src="https://unpkg.com/scrollreveal"></script>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Main section -->
    <section class="main-section">
        <img src="images/hero.jpg" alt="" class="parallax">
        <div class="container">
            <!-- Login form -->
             <div class="login-box">
                 <form class="login-form" method="POST" action="login.php">
                    <div class="login-head">
                        <a href="index.php">
                            <img src="images/vg-logo-2.svg" alt="">
                        </a>
                        <h4>Connexion</h4>
                    </div>
                    <div class="login-content">
                         <h4>Email</h4>
                         <input
                             type="email"
                             name="email"
                             required
                         >
     
                         <h4>Mot de passe</h4>
                         <input
                             type="password"
                             name="password"
                             required
                         >
     
                        <div class="login-buttons">
                            <button type="submit" class="btn body-btn btn-underline">
                                Se connecter
                            </button>
                            <a href="index.php" class="btn body-btn btn-underline">
                                Accueil
                            </a>
                        </div> 
                     </div>
                 </form>
             </div>
        </div>
    </section>

    <script type="module" src="js/main.js"></script>
</body>
</html>