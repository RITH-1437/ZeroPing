<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Routing\Router;
use App\Core\Routing\Route;
use App\Core\Container\Container;

class MiddlewareTest extends \Tests\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $ref = new \ReflectionProperty(Router::class, 'routes');
        $ref->setValue(null, []);
        $ref2 = new \ReflectionProperty(Router::class, 'prefix');
        $ref2->setValue(null, '');
        $ref3 = new \ReflectionProperty(Router::class, 'groupMiddleware');
        $ref3->setValue(null, []);
    }

    public function testGetRouteWithMiddleware(): void
    {
        $route = Router::get('/admin', ['AdminController', 'index'], ['auth']);

        $this->assertSame(['auth'], $route->middleware);
    }

    public function testPostRouteWithMultipleMiddleware(): void
    {
        $route = Router::post('/admin/users', ['AdminController', 'store'], ['auth', 'csrf', 'throttle']);

        $this->assertCount(3, $route->middleware);
        $this->assertSame(['auth', 'csrf', 'throttle'], $route->middleware);
    }

    public function testGroupMiddlewareMergesWithRouteMiddleware(): void
    {
        Router::middleware(['auth'], function () {
            Router::get('/dashboard', ['DashboardController', 'index'], ['throttle']);
        });

        $routes = Router::routes();
        $route = $routes['GET']['/dashboard'];

        $this->assertSame(['auth', 'throttle'], $route->middleware);
    }

    public function testMiddlewareGroupRestoresAfterCallback(): void
    {
        Router::middleware(['auth'], function () {
            Router::get('/protected', ['ProtectedController', 'index']);
        });

        Router::get('/public', ['PublicController', 'index']);

        $routes = Router::routes();

        $this->assertSame(['auth'], $routes['GET']['/protected']->middleware);
        $this->assertSame([], $routes['GET']['/public']->middleware);
    }

    public function testPrefixWithMiddlewareGroup(): void
    {
        Router::prefix('/api', function () {
            Router::middleware(['auth:api'], function () {
                Router::get('/users', ['UserController', 'index']);
            });
        });

        $routes = Router::routes();

        $this->assertArrayHasKey('/api/users', $routes['GET']);
        $this->assertSame(['auth:api'], $routes['GET']['/api/users']->middleware);
    }

    public function testNestedPrefixAndMiddlewareGroups(): void
    {
        Router::prefix('/admin', function () {
            Router::middleware(['auth', 'admin'], function () {
                Router::get('/users', ['AdminController', 'users'], ['throttle:60']);
            });
        });

        $routes = Router::routes();

        $this->assertArrayHasKey('/admin/users', $routes['GET']);
        $this->assertSame(['auth', 'admin', 'throttle:60'], $routes['GET']['/admin/users']->middleware);
    }

    public function testMiddlewareDoesNotBleedAcrossRoutes(): void
    {
        Router::middleware(['auth'], function () {
            Router::get('/a', ['A', 'index']);
        });

        Router::get('/b', ['B', 'index']);

        Router::middleware(['csrf'], function () {
            Router::get('/c', ['C', 'index']);
        });

        $routes = Router::routes();

        $this->assertSame(['auth'], $routes['GET']['/a']->middleware);
        $this->assertSame([], $routes['GET']['/b']->middleware);
        $this->assertSame(['csrf'], $routes['GET']['/c']->middleware);
    }

    public function testRouteWithNoMiddleware(): void
    {
        $route = Router::get('/home', ['HomeController', 'index']);

        $this->assertSame([], $route->middleware);
    }
}