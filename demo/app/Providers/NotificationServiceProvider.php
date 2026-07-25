<?php

namespace App\Providers;

use App\Core\Container\Container;
use App\Core\Notifications\Channels\DatabaseChannel;
use App\Core\Notifications\Channels\LogChannel;
use App\Core\Notifications\Channels\MailChannel;
use App\Core\Notifications\NotificationManager;
use Zeroping\Support\ServiceProvider as PackageServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(NotificationManager::class, function (Container $app) {
            $manager = new NotificationManager($app);

            $manager->channel('mail', $app->make(MailChannel::class));
            $manager->channel('log', $app->make(LogChannel::class));
            $manager->channel('database', $app->make(DatabaseChannel::class));

            // Package-declared channels (registered via ServiceProvider::channels()).
            foreach (PackageServiceProvider::allChannels() as $name => $class) {
                if (!isset($manager->channels()[$name])) {
                    $manager->channel($name, $app->make($class));
                }
            }

            return $manager;
        });
    }
}
