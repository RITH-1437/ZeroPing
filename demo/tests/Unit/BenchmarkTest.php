<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Benchmark\Benchmark;
use PHPUnit\Framework\TestCase;

class BenchmarkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Benchmark::reset();
    }

    public function test_measure_returns_callback_result_and_records(): void
    {
        $result = Benchmark::measure(static fn() => 21 * 2, 'math');

        $this->assertSame(42, $result);

        $results = Benchmark::results();

        $this->assertArrayHasKey('math', $results);
        $this->assertIsFloat($results['math']['time']);
        $this->assertGreaterThanOrEqual(0, $results['math']['time']);
        $this->assertIsInt($results['math']['memory']);
    }

    public function test_time_returns_float(): void
    {
        $time = Benchmark::time(static function () {
            usleep(1000);
        });

        $this->assertIsFloat($time);
        $this->assertGreaterThan(0.0, $time);
    }

    public function test_memory_returns_int(): void
    {
        $memory = Benchmark::memory(static function () {
            $x = str_repeat('x', 1024);
            unset($x);
        });

        $this->assertIsInt($memory);
        $this->assertGreaterThanOrEqual(0, $memory);
    }

    public function test_summary_contains_results(): void
    {
        Benchmark::measure(static fn() => 1, 'a');

        $this->assertStringContainsString('Benchmark results', Benchmark::summary());
        $this->assertStringContainsString('a', Benchmark::summary());
    }

    public function test_reset_clears_results(): void
    {
        Benchmark::measure(static fn() => 1, 'a');
        Benchmark::reset();

        $this->assertSame([], Benchmark::results());
    }
}
