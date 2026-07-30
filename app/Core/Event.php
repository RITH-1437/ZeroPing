<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Simple static event dispatcher using string-based event names.
 *
 * @deprecated Use {@see \App\Core\Events\EventDispatcher} instead, which supports
 *             class-based events, listener priority, and propagation stopping
 *             through the service container. This class is kept for backwards
 *             compatibility but may be removed in a future release.
 *
 * @example
 * ```php
 * // Register a listener
 * Event::listen('user.registered', function (array $data) {
 *     // Handle the event...
 * });
 *
 * // Dispatch the event
 * Event::dispatch('user.registered', ['user_id' => 1]);
 * ```
 */
class Event
{
    /**
     * The registered event listeners.
     *
     * @var array<string, array<int, callable>>
     */
    protected static array $listeners = [];

    /**
     * Register an event listener for a named event.
     *
     * @param string $event The event name.
     * @param callable $listener The callback to invoke when the event is dispatched.
     * @return void
     */
    public static function listen(string $event, callable $listener): void
    {
        static::$listeners[$event][] = $listener;
    }

    /**
     * Dispatch a named event to all registered listeners.
     *
     * Each listener receives the payload as its first argument.
     *
     * @param string $event The event name.
     * @param mixed $payload The data to pass to each listener.
     * @return void
     */
    public static function dispatch(string $event, mixed $payload = null): void
    {
        if (!isset(static::$listeners[$event])) {
            return;
        }

        foreach (static::$listeners[$event] as $listener) {
            $listener($payload);
        }
    }

    /**
     * Check if any listeners are registered for the given event.
     *
     * @param string $event The event name.
     * @return bool True if at least one listener is registered.
     */
    public static function hasListeners(string $event): bool
    {
        return !empty(static::$listeners[$event]);
    }

    /**
     * Remove all listeners for a specific event, or all listeners entirely.
     *
     * @param string|null $event The event name, or null to clear all listeners.
     * @return void
     */
    public static function forget(?string $event = null): void
    {
        if ($event === null) {
            static::$listeners = [];
        } else {
            unset(static::$listeners[$event]);
        }
    }
}
