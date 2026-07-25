<?php

namespace App\Core\Notifications\Channels;

use App\Core\Notifications\Notification;

interface Channel
{
    /**
     * Deliver the notification payload over this channel.
     *
     * @param mixed $payload  The to<Channel>() result for this channel.
     */
    public function send(object $notifiable, Notification $notification, mixed $payload): void;
}
