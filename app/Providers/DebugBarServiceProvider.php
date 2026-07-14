<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Container\Container;
use App\Core\Debug\DebugBar;

class DebugBarServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            DebugBar::class,
            fn() => new DebugBar()
        );
    }
}
