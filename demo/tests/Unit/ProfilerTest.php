<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Profiler\Profiler;
use PHPUnit\Framework\TestCase;

class ProfilerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Profiler::reset();
    }

    public function test_span_returns_result_and_records(): void
    {
        $result = Profiler::span('work', static fn() => 'done');

        $this->assertSame('done', $result);
        $this->assertArrayHasKey('work', Profiler::spans());
        $this->assertArrayHasKey('time', Profiler::spans()['work']);
    }

    public function test_report_contains_name_and_total(): void
    {
        Profiler::span('alpha', static fn() => usleep(500));
        Profiler::span('beta', static fn() => usleep(500));

        $report = Profiler::report();

        $this->assertStringContainsString('Profiler', $report);
        $this->assertStringContainsString('alpha', $report);
        $this->assertStringContainsString('beta', $report);
        $this->assertStringContainsString('TOTAL', $report);
        $this->assertGreaterThan(0.0, Profiler::totalTime());
    }

    public function test_add_and_reset(): void
    {
        Profiler::add('manual', 0.123, 2048);

        $this->assertArrayHasKey('manual', Profiler::spans());
        $this->assertSame(0.123, Profiler::spans()['manual']['time']);

        Profiler::reset();

        $this->assertSame([], Profiler::spans());
    }
}
