<?php

namespace App\Core\Notifications;

use App\Core\Application\App;

/**
 * Base class for all notifications.
 *
 * A notification declares which channels it uses via via(), and exposes a
 * to<Channel>() method per channel returning the payload that channel needs
 * (a Mailable for "mail", an array for "database"/"log").
 *
 * Dispatch through Notification::send($notifiable, $notification) or the
 * Notifiable trait's notify() helper.
 */
abstract class Notification
{
    /**
     * Channels this notification is delivered through.
     *
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Payload for the "mail" channel (a Mailable instance).
     */
    public function toMail(object $notifiable): mixed
    {
        return null;
    }

    /**
     * Payload for the "database" channel.
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Payload for the "log" channel.
     */
    public function toLog(object $notifiable): array
    {
        return $this->toArray($notifiable);
    }

    /**
     * Payload for array-based channels (database/log) by default.
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }

    /**
     * Send a notification to a notifiable.
     */
    public static function send(object $notifiable, Notification $notification): void
    {
        $manager = App::container()->make(NotificationManager::class);
        $manager->send($notifiable, $notification);
    }
}
