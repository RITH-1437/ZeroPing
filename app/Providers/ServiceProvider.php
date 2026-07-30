<?php

declare(strict_types=1);

namespace App\Providers;

use App\Core\Container\Container;
use App\Core\Scheduling\Schedule;

/**
 * Base class for all service providers.
 *
 * Service providers are the central place to configure and bootstrap
 * application services. Each provider's register() method binds services
 * into the container; the optional boot() method runs after all providers
 * have been registered, allowing cross-provider interaction.
 *
 * Providers may also declare deferred loading, event listeners, and
 * scheduled tasks.
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/introduction
 */
abstract class ServiceProvider
{
    /**
     * The application DI container instance.
     */
    protected Container $container;

    /**
     * Create a new service provider instance.
     *
     * @param Container $container The application container.
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Register bindings into the container.
     *
     * This method is called for every provider before any boot() methods
     * run. Only bind things into the container here — do not attempt to
     * use other services as they may not yet be registered.
     */
    abstract public function register(): void;

    /**
     * Bootstrap any application services.
     *
     * Called after all providers have been registered. Use this to perform
     * actions that depend on other services being available (event
     * subscriptions, view composers, route registrations, etc.).
     */
    public function boot(): void
    {
        // Optional — override in subclass.
    }

    /**
     * Services this provider offers (used for deferred providers).
     *
     * When isDeferred() returns true, the framework will only register and
     * boot this provider when one of the listed abstracts is first resolved.
     *
     * @return array<int, class-string> List of abstract types this provider binds.
     */
    public function provides(): array
    {
        return [];
    }

    /**
     * Whether this provider should be deferred.
     *
     * Deferred providers are not registered until one of their provided
     * services is requested from the container, improving boot performance
     * for services that are not used on every request.
     */
    public function isDeferred(): bool
    {
        return false;
    }

    /**
     * Event listeners this provider registers.
     *
     * Return an associative array where keys are event class names and values
     * are listener class names (or arrays of listener class names).
     *
     * @return array<class-string, class-string<\App\Core\Events\Listener>|array<int,
     *                              class-string<\App\Core\Events\Listener>>>
     */
    public function listens(): array
    {
        return [];
    }

    /**
     * Define scheduled tasks for this provider.
     *
     * Called during the scheduling phase. Use the provided Schedule instance
     * to register recurring tasks (cron jobs, interval-based runs, etc.).
     *
     * @param Schedule $schedule The scheduler instance.
     */
    public function schedules(Schedule $schedule): void
    {
        // Optional — override in subclass.
    }
}
