<?php

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;
use App\Core\Support\Log;

class SMTPDriver implements MailDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function send(Mailable $mailable): bool
    {
        // This is a simplified implementation. A real implementation would
        // use a library like PHPMailer or Swift Mailer.
        $to = implode(', ', $mailable->getTo());
        $subject = $mailable->getSubject();
        $body = $mailable->getBody();
        $headers = 'From: ' . $this->config['from']['address'];

        Log::info("Sending email to {$to} with subject '{$subject}' using SMTP driver.");

        return mail($to, $subject, $body, $headers);
    }
}
