<?php

namespace App\Providers;

use App\Core\Mail\MailManager;

class MailServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(MailManager::class, function () {
            return new MailManager();
        });
    }
}
