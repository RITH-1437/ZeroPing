<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Application\App;
use App\Core\Http\Kernel;
use App\Core\Routing\Router;
use PHPUnit\Framework\TestCase;

class SortKernel extends Kernel
{
    public array $middleware       = [];
    public array $middlewarePriority = [];

    public function sort(array $names): array
    {
        return $this->sortMiddleware($names);
    }

    public function resolve(string $name): string
    {
        return $this->resolveMiddlewareClass($name);
    }
}

class KernelTest extends TestCase
{
    private Kernel $kernel;

    protected function setUp(): void
    {
        $this->kernel = new SortKernel(new App(BASE_PATH));
    }

    public function testMiddlewareSortedByPriority(): void
    {
        $this->kernel->middleware       = ['b', 'a', 'c'];
        $this->kernel->middlewarePriority = ['a', 'b'];

        $this->assertSame(['a', 'b', 'c'], $this->kernel->sort(['b', 'a', 'c']));
    }

    public function testUnlistedMiddlewareKeepsOrderAfterPriority(): void
    {
        $this->kernel->middlewarePriority = ['x'];

        $this->assertSame(['x', 'y', 'z'], $this->kernel->sort(['y', 'x', 'z']));
    }

    public function testResolveShortMiddlewareName(): void
    {
        $this->assertSame(
            'App\Http\Middleware\AuthMiddleware',
            $this->kernel->resolve('auth')
        );
    }

    public function testResolveFullyQualifiedClassName(): void
    {
        $this->assertSame(self::class, $this->kernel->resolve(self::class));
    }

    public function testMiddlewareGroupExpansion(): void
    {
        Router::middlewareGroup('g1', ['auth']);
        Router::middlewareGroup('g2', ['g1', 'guest']);

        $this->assertSame(['auth', 'guest'], Router::expandMiddleware(['g2']));
    }
}
