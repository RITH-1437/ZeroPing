<?php

declare(strict_types=1);

namespace App\Core\Benchmark;

use App\Core\Debug\Performance;

/**
 * Lightweight benchmarking utility. Measures execution time and memory
 * usage of callables and records named results for later reporting.
 */
class Benchmark
{
    protected static array $results = [];

    public static function measure(callable $callback, ?string $name = null): mixed
    {
        $name = $name ?? self::nextName();

        $startMemory = memory_get_usage();

        Performance::start($name);
        $result = $callback();
        Performance::stop($name);
        $endMemory = memory_get_usage();

        self::$results[$name] = [
            'time'   => Performance::get($name)['time'] ?? 0.0,
            'memory' => max(0, $endMemory - $startMemory),
            'peak'   => memory_get_peak_usage(),
        ];

        return $result;
    }

    public static function time(callable $callback, ?string $name = null): float
    {
        $name = $name ?? self::nextName();

        Performance::start($name);
        $callback();
        Performance::stop($name);

        return Performance::get($name)['time'] ?? 0.0;
    }

    public static function memory(callable $callback, ?string $name = null): int
    {
        $name = $name ?? self::nextName();

        $start = memory_get_usage();
        $callback();

        return max(0, memory_get_usage() - $start);
    }

    public static function results(): array
    {
        return self::$results;
    }

    public static function summary(): string
    {
        $lines = ['Benchmark results:'];

        foreach (self::$results as $name => $result) {
            $lines[] = sprintf(
                '  %s: %.4fs, %s',
                $name,
                $result['time'] ?? 0.0,
                self::humanBytes($result['memory'] ?? 0)
            );
        }

        return implode(PHP_EOL, $lines);
    }

    public static function reset(): void
    {
        self::$results = [];

        Performance::reset();
    }

    protected static function nextName(): string
    {
        return 'bench_' . (count(self::$results) + 1);
    }

    protected static function humanBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $value = (float) $bytes;
        $unit = 0;

        while ($value >= 1024 && $unit < count($units) - 1) {
            $value /= 1024;
            $unit++;
        }

        return round($value, 2) . ' ' . $units[$unit];
    }
}
