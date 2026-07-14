<?php

namespace App\Core\Notifications\Channels;

use App\Core\Notifications\Notification;
use App\Core\Support\Log;

class LogChannel implements Channel
{
    public function send(object $notifiable, Notification $notification, mixed $payload): void
    {
        $message = is_array($payload) ? (string) json_encode($payload) : (string) $payload;

        Log::info('[notification] ' . $message);
    }
}
