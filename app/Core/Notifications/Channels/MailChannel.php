<?php

namespace App\Core\Notifications\Channels;

use App\Core\Mail\MailManager;
use App\Core\Mail\Mailable;
use App\Core\Notifications\Notification;

class MailChannel implements Channel
{
    public function __construct(protected MailManager $mail)
    {
    }

    public function send(object $notifiable, Notification $notification, mixed $payload): void
    {
        if (!$payload instanceof Mailable) {
            return;
        }

        $this->mail->send($payload);
    }
}
