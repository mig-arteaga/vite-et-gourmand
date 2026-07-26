<?php
require "assets/auth.php";

requireRoles(["Administrateur", "Employé", "Utilisateur"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mon compte - Vite & Gourmand</title>
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
    <section class="employe-section first-section">
        <div class="container">
            <h2 class="sub-headline title-down">
                <span class="first-letter">M</span>on
            </h2>
            <h1 class="headline title-up">Compte</h1>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>