<?php

declare(strict_types=1);

namespace App\Controllers;

class ContactController {
    public function send(
        string $name,
        string $email,
        string $subject,
        string $message
    ): bool {
        $mailTo = 'contact@vitegourmand.com';
        $headers = 'De : ' . $email;

        $text =
            "Vous avez reçu un mail de {$name}.\n" .
            "Mail : {$email}.\n\n" .
            $message;

        return mail($mailTo, $subject, $text, $headers);
    }
}