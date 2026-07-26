<?php

declare(strict_types=1);

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
        $to = implode(', ', $mailable->getTo());
        $subject = $mailable->getSubject();
        $body = $mailable->getBody();

        $fromAddress = $this->config['from']['address'] ?? 'no-reply@localhost';
        $fromName = $this->config['from']['name'] ?? '';

        $subject = str_replace(["\r", "\n"], '', $subject);
        $fromAddress = str_replace(["\r", "\n"], '', $fromAddress);

        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
        if ($fromName !== '') {
            $headers .= 'From: ' . $fromName . ' <' . $fromAddress . '>' . "\r\n";
        } else {
            $headers .= 'From: ' . $fromAddress . "\r\n";
        }

        Log::info("Sending email to {$to} with subject '{$subject}' using SMTP driver.");

        return mail($to, $subject, $body, $headers);
    }
}
