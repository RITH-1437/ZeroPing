<?php

namespace App\Providers;

use App\Core\Container\Container;
use App\Core\Scheduling\Schedule;

abstract class ServiceProvider
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    abstract public function register(): void;

    public function boot(): void
    {
        // Optional
    }

    /**
     * Services this provider offers (used by deferred providers).
     *
     * @return array<int, class-string>
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * When true, boot() is deferred until one of provides() is resolved.
     */
    public function isDeferred(): bool
    {
        return false;
    }

    /**
     * Event listeners this provider registers.
     *
     * @return array<string, class-string<\App\Core\Events\Listener>|class-string<\App\Core\Events\Listener>[]>
     */
    public function listens(): array
    {
        return [];
    }

    /**
     * Scheduled events this provider registers.
     */
    public function schedules(Schedule $schedule): void
    {
        // Optional
    }
}
