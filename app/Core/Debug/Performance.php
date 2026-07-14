<?php

namespace App\Core\Debug;

class Performance
{
    protected static array $timers = [];

    public static function start(string $name): void
    {
        static::$timers[$name] = [
            'start' => microtime(true),
        ];
    }

    public static function stop(string $name): void
    {
        static::$timers[$name]['end'] = microtime(true);
        static::$timers[$name]['time'] = static::$timers[$name]['end'] - static::$timers[$name]['start'];
    }

    public static function get(string $name): ?array
    {
        return static::$timers[$name] ?? null;
    }

    public static function all(): array
    {
        return static::$timers;
    }

    public static function reset(): void
    {
        static::$timers = [];
    }
}
