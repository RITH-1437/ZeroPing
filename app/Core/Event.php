<?php

namespace App\Core;

class Event
{
    /**
     * The event listeners.
     *
     * @var array
     */
    protected static array $listeners = [];

    /**
     * Register an event listener.
     *
     * @param  string  $event
     * @param  callable  $listener
     * @return void
     */
    public static function listen(string $event, callable $listener): void
    {
        static::$listeners[$event][] = $listener;
    }

    /**
     * Dispatch an event.
     *
     * @param  string  $event
     * @param  mixed  $payload
     * @return void
     */
    public static function dispatch(string $event, mixed $payload): void
    {
        if (isset(static::$listeners[$event])) {
            foreach (static::$listeners[$event] as $listener) {
                $listener($payload);
            }
        }
    }
}
