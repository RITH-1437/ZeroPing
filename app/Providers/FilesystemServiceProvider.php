<?php

namespace App\Providers;

use App\Core\Filesystem\FilesystemManager;
use App\Core\Container\Container;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(FilesystemManager::class, function (Container $app) {
            return new FilesystemManager($app);
        });
    }
}
