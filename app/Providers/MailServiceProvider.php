<?php

namespace App\Providers;

use App\Core\Mail\MailManager;
use App\Core\Container\Container;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MailManager::class, function (Container $app) {
            return new MailManager($app);
        });
    }
}
