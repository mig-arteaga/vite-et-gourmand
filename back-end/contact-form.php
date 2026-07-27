<?php

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $mailFrom = $_POST['mail'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $mailTo = "contact@vitegourmand.com";
    $headers = "De : ".$mailFrom;
    // $txt = "Vous avez reçu un mail de ".$name.".\n\n".$message;
    $txt = "Vous avez reçu un mail de ".$name.".\n"."Mail : ".$mailFrom.".\n\n".$message;

    mail($mailTo, $subject, $txt, $headers);
    header("Location: ../index.php");
}

?>