<?php

declare(strict_types=1);

namespace App\Services;

class MailService {
    private const FROM_EMAIL = 'no-reply@vite-et-gourmand.fr';

    public function sendOrderConfirmation(
        string $toEmail,
        string $firstName,
        int $orderId,
        string $menuTitle,
        float $totalPrice,
        string $deliveryDate,
        string $deliveryTime
    ): bool {
        $subject = "Confirmation de votre commande n°{$orderId} - Vite & Gourmand";
        $body =
            "Bonjour {$firstName},\n\n" .
            "Nous vous confirmons l'enregistrement de votre commande n°{$orderId}.\n" .
            "Menu choisi : {$menuTitle}\n" .
            "Livraison prévue le : {$deliveryDate} à {$deliveryTime}\n" .
            "Montant total : " . number_format($totalPrice, 2, ',', ' ') . " €\n\n" .
            "Vous pouvez suivre l'état de votre commande depuis votre espace personnel.\n\n" .
            "L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    public function sendEquipmentReturnNotice(
        string $toEmail,
        string $firstName,
        int $orderId
    ): bool {
        $subject = "Retour de matériel pour la commande n°{$orderId} - Vite & Gourmand";
        $body =
            "Bonjour {$firstName},\n\n" .
            "Votre commande n°{$orderId} incluait un prêt de matériel.\n" .
            "Nous vous rappelons que vous disposez d'un délai de 10 jours ouvrés pour restituer ce matériel en prenant contact avec notre société.\n" .
            "Conformément à nos conditions générales de vente, à défaut de restitution dans ce délai, des frais forfaitaires de 600 € vous seront facturés.\n\n" .
            "L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    public function sendOrderCompletedReviewNotice(
        string $toEmail,
        string $firstName,
        int $orderId
    ): bool {
        $subject = "Votre commande n°{$orderId} est terminée - Donnez votre avis !";
        $body =
            "Bonjour {$firstName},\n\n" .
            "Votre commande n°{$orderId} est désormais terminée.\n" .
            "Vous pouvez dès à présent vous connecter à votre espace personnel pour laisser une note (de 1 à 5) et un commentaire sur votre prestation.\n\n" .
            "Merci de votre confiance,\n" .
            "L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    public function sendEmployeeAccountCreatedNotice(
        string $toEmail,
        string $firstName,
        string $lastName
    ): bool {
        $subject = "Création de votre compte collaborateur - Vite & Gourmand";
        $body =
            "Bonjour {$firstName} {$lastName},\n\n" .
            "Votre compte collaborateur (Employé) vient d'être créé par l'administrateur sur l'application Vite & Gourmand.\n" .
            "Identifiant de connexion (adresse mail) : {$toEmail}\n\n" .
            "Pour des raisons de sécurité, votre mot de passe ne figure pas dans cet e-mail. Veuillez vous rapprocher de l'administrateur afin de récupérer votre mot de passe de connexion.\n\n" .
            "L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    public function sendWelcomeEmail(
        string $toEmail,
        string $firstName,
        string $lastName
    ): bool {
        $subject = "Bienvenue chez Vite & Gourmand, {$firstName} !";
        $body =
            "Bonjour {$firstName} {$lastName},\n\n" .
            "Nous vous souhaitons la bienvenue chez Vite & Gourmand !\n" .
            "Votre compte client est désormais actif avec l'identifiant : {$toEmail}.\n\n" .
            "Vous pouvez dès à présent découvrir nos menus sur mesure, passer commande et suivre vos prestations depuis votre espace personnel.\n\n" .
            "À très bientôt,\n" .
            "Julie & José — L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    public function sendPasswordResetEmail(
        string $toEmail,
        string $firstName,
        string $resetLink
    ): bool {
        $subject = "Réinitialisation de votre mot de passe - Vite & Gourmand";
        $body =
            "Bonjour {$firstName},\n\n" .
            "Nous avons reçu une demande de réinitialisation du mot de passe associé à votre compte Vite & Gourmand.\n" .
            "Pour choisir un nouveau mot de passe (lien valable 1 heure), cliquez sur le lien suivant :\n" .
            "{$resetLink}\n\n" .
            "Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet e-mail en toute sécurité.\n\n" .
            "L'équipe Vite & Gourmand";

        return $this->send($toEmail, $subject, $body);
    }

    private function send(string $to, string $subject, string $body): bool {
        $headers =
            'From: ' . self::FROM_EMAIL . "\r\n" .
            'Reply-To: ' . self::FROM_EMAIL . "\r\n" .
            'Content-Type: text/plain; charset=UTF-8';

        error_log("[MailService] To: {$to} | Subject: {$subject}");

        return @mail($to, $subject, $body, $headers);
    }
}

