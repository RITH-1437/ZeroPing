<?php

declare(strict_types=1);

namespace App\Core\Profiler;

use App\Core\Benchmark\Benchmark;

/**
 * Aggregates named benchmark spans into a single profile and renders a
 * human-readable report. Sits on top of the Benchmark utility.
 */
class Profiler
{
    protected static array $spans = [];

    public static function span(string $name, callable $callback): mixed
    {
        $result = Benchmark::measure($callback, $name);

        self::$spans[$name] = Benchmark::results()[$name] ?? [];

        return $result;
    }

    public static function add(string $name, float $time, int $memory = 0): void
    {
        self::$spans[$name] = [
            'time'   => $time,
            'memory' => $memory,
            'peak'   => memory_get_peak_usage(),
        ];
    }

    public static function spans(): array
    {
        return self::$spans;
    }

    public static function totalTime(): float
    {
        $total = 0.0;

        foreach (self::$spans as $span) {
            $total += $span['time'] ?? 0.0;
        }

        return $total;
    }

    public static function report(): string
    {
        $lines = ['Profiler:'];

        foreach (self::$spans as $name => $span) {
            $lines[] = sprintf('  %s: %.4fs', $name, $span['time'] ?? 0.0);
        }

        $lines[] = sprintf('  TOTAL: %.4fs', self::totalTime());

        return implode(PHP_EOL, $lines);
    }

    public static function reset(): void
    {
        self::$spans = [];

        Benchmark::reset();
    }
}
