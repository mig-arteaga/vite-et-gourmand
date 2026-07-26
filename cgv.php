<?php
    require_once "back-end/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Conditions générales de vente - Vite & Gourmand</title>
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
                <span class="first-letter">C</span>onditions
            </h2>
            <h1 class="headline title-up">Générales de vente</h1>
            <h3>Objet</h3>
            <p>
                Les présentes Conditions Générales de Vente définissent les règles applicables
                aux services proposés par Vite & Gourmand, notamment la consultation des menus
                et la prise de commande.
            </p>

            <h3>Commandes</h3>
            <p>
                Toute commande effectuée auprès de Vite & Gourmand implique l'acceptation
                des présentes conditions générales de vente.
            </p>
            <p>
                Les informations fournies par le client doivent être exactes afin de permettre
                le bon traitement de la commande.
            </p>

            <h3>Validation et suivi des commandes</h3>
            <p>
                Après réception d'une commande, celle-ci est vérifiée et validée par l'équipe
                de Vite & Gourmand. Le client peut être informé de son évolution selon les
                différents statuts de suivi disponibles.
            </p>

            <h3>Paiement</h3>
            <p>
                Le règlement des commandes doit être effectué selon les moyens de paiement
                proposés par l'établissement.
            </p>

            <h3>Livraison et retrait</h3>
            <p>
                Les modalités de livraison ou de retrait sont définies lors de la commande.
                Le client doit fournir des informations exactes afin d'assurer le bon déroulement
                du service.
            </p>

            <h3>Retour de matériel</h3>
            <p>
                Dans le cas où du matériel est prêté au client, celui-ci doit être restitué
                selon les conditions indiquées par l'établissement.
            </p>
            <p>
                En cas de non-restitution dans un délai de 10 jours ouvrés après demande de retour,
                des frais de 600 euros pourront être appliqués conformément aux conditions prévues.
            </p>

            <h3>Responsabilités</h3>
            <p>
                Vite & Gourmand s'engage à fournir un service conforme aux informations
                présentées sur son site.
            </p>

            <h3>Acceptation des conditions</h3>
            <p>
                Toute utilisation du site ou commande passée auprès de Vite & Gourmand implique
                l'acceptation complète des présentes Conditions Générales de Vente.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <?php
        require "assets/footer.php";
    ?>

    <script type="module" src="js/main.js"></script>
</body>
</html>