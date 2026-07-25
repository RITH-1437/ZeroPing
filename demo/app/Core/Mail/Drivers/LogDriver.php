<?php

namespace App\Core\Mail\Drivers;

use App\Core\Mail\Mailable;
use App\Core\Support\Log;

class LogDriver implements MailDriver
{
    public function send(Mailable $mailable): bool
    {
        $to = implode(', ', $mailable->getTo());
        $subject = $mailable->getSubject();
        $body = $mailable->getBody();

        Log::info("Sending email to {$to} with subject '{$subject}' using log driver.");
        Log::info("Body: {$body}");

        return true;
    }
}
