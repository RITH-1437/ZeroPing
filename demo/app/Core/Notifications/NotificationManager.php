<?php

namespace App\Core\Notifications;

use App\Core\Container\Container;
use App\Core\Notifications\Channels\Channel;

/**
 * Routes a notification to each of its declared channels.
 *
 * Channels are registered by NotificationServiceProvider (framework defaults) and
 * by packages through ServiceProvider::channels().
 */
class NotificationManager
{
    /** @var array<string, Channel> */
    protected array $channels = [];

    public function __construct(protected Container $container)
    {
    }

    public function channel(string $name, Channel $channel): void
    {
        $this->channels[$name] = $channel;
    }

    /**
     * @return array<string, Channel>
     */
    public function channels(): array
    {
        return $this->channels;
    }

    public function send(object $notifiable, Notification $notification): void
    {
        foreach ($notification->via($notifiable) as $name) {
            $payload = $this->payloadFor($notification, $name, $notifiable);

            if ($payload === null) {
                continue;
            }

            $channel = $this->channels[$name] ?? ($this->channels['log'] ?? null);

            if ($channel === null) {
                continue;
            }

            $channel->send($notifiable, $notification, $payload);
        }
    }

    private function payloadFor(Notification $notification, string $name, object $notifiable): mixed
    {
        $method = 'to' . ucfirst($name);

        if (!method_exists($notification, $method)) {
            return null;
        }

        return $notification->$method($notifiable);
    }
}
