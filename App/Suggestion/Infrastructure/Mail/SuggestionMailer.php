<?php

namespace App\Suggestion\Infrastructure\Mail;

use App\Suggestion\Domain\Entity\Suggestion;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class SuggestionMailer
{
    public function send(Suggestion $suggestion): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $_ENV['MAIL_HOST'] ?? '';
            $mail->Port = (int) ($_ENV['MAIL_PORT'] ?? 587);
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'] ?? '';
            $mail->Password = $_ENV['MAIL_PASSWORD'] ?? '';

            $mail->setFrom(
                $_ENV['MAIL_FROM_EMAIL'] ?? 'no-reply@example.com',
                $_ENV['MAIL_FROM_NAME'] ?? 'Media Library'
            );
            $mail->addReplyTo($suggestion->getEmail(), $suggestion->getName());
            $mail->addAddress($_ENV['MAIL_FROM_EMAIL'] ?? 'no-reply@example.com');

            $mail->Subject = 'Library Suggestion from: ' . $suggestion->getName();
            $mail->Body = $suggestion->getEmailBody();

            return $mail->send();
        } catch (Exception $e) {
            error_log('[SuggestionMailer] ' . $e->getMessage());
            return false;
        }
    }
}
