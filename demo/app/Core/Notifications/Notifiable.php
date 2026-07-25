<?php

namespace App\Core\Notifications;

/**
 * Gives a model/user the ability to receive notifications.
 */
trait Notifiable
{
    public function notify(Notification $notification): void
    {
        Notification::send($this, $notification);
    }

    public function notifyNow(Notification $notification): void
    {
        Notification::send($this, $notification);
    }

    /**
     * Routing address for a channel (e.g. the email for "mail").
     */
    public function routeNotificationFor(string $channel): mixed
    {
        return null;
    }
}
