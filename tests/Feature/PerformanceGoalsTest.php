<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Benchmark\Benchmark;
use App\Core\Http\Response;
use App\Core\Routing\Router;
use App\Core\Testing\TestCase;

/**
 * Performance goals: representative requests must stay within a latency budget.
 */
class PerformanceGoalsTest extends TestCase
{
    public function test_simple_route_within_latency_budget(): void
    {
        Router::get('/_zp_perf', static fn() => new Response('ok'));

        $time = Benchmark::time(fn() => $this->get('/_zp_perf'));

        // A trivial in-process dispatch should complete well under 500ms.
        $this->assertLessThan(0.5, $time);
    }
}
