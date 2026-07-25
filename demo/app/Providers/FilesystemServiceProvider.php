<?php

namespace App\Providers;

use App\Core\Filesystem\FilesystemManager;

class FilesystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(FilesystemManager::class, function () {
            return new FilesystemManager();
        });
    }
}
