<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Application\App;
use App\Core\Http\Kernel;
use App\Http\Middleware\Middleware;
use App\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

class HttpKernelProbeMiddleware extends Middleware
{
    public static bool $ran = false;

    public function handle(): void
    {
        self::$ran = true;
    }
}

class HttpKernelTest extends TestCase
{
    protected function setUp(): void
    {
        HttpKernelProbeMiddleware::$ran = false;
    }

    public function testHandleRunsGlobalMiddlewareAndDispatchesRoute(): void
    {
        $uri = '/kernel-probe-' . uniqid();

        Router::get($uri, function (): string {
            return 'KERNEL_PROBE_OK';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI']   = $uri;

        $app = new App(BASE_PATH);

        $kernel = new class ($app) extends Kernel {
            protected array $middleware = [HttpKernelProbeMiddleware::class];
        };

        ob_start();
        $kernel->handle();
        $output = (string) ob_get_clean();

        $this->assertStringContainsString('KERNEL_PROBE_OK', $output);
        $this->assertTrue(HttpKernelProbeMiddleware::$ran, 'Global middleware should run before dispatch.');
    }
}
