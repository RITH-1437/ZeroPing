<?php

namespace App\Listeners;

use App\Core\Events\Event;
use App\Core\Events\Listener;

class LogUserRegistered implements Listener
{
    public function handle(Event $event): void
    {
        echo "User registered: {$event->user['email']}" . PHP_EOL;
    }
}
