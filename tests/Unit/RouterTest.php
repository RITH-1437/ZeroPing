<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Routing\Router;
use App\Core\Routing\Route;

class RouterTest extends \Tests\TestCase
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

    public function testGetRegistersGetRoute(): void
    {
        $route = Router::get('/users', ['UserController', 'index']);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('GET', $route->method);
        $this->assertSame('/users', $route->uri);
    }

    public function testPostRegistersPostRoute(): void
    {
        $route = Router::post('/users', ['UserController', 'store']);

        $this->assertInstanceOf(Route::class, $route);
        $this->assertSame('POST', $route->method);
        $this->assertSame('/users', $route->uri);
    }

    public function testGetWithMiddlewareAttachesMiddleware(): void
    {
        $route = Router::get('/admin', ['AdminController', 'index'], ['auth']);

        $this->assertSame(['auth'], $route->middleware);
    }

    public function testPostWithMiddlewareAttachesMiddleware(): void
    {
        $route = Router::post('/admin', ['AdminController', 'store'], ['auth', 'csrf']);

        $this->assertSame(['auth', 'csrf'], $route->middleware);
    }

    public function testPrefixAddsPrefixToRoutes(): void
    {
        Router::prefix('/api', function () {
            Router::get('/users', ['UserController', 'index']);
            Router::post('/users', ['UserController', 'store']);
        });

        $routes = Router::routes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/api/users', $routes['GET']);
        $this->assertArrayHasKey('POST', $routes);
        $this->assertArrayHasKey('/api/users', $routes['POST']);
    }

    public function testPrefixRestoresAfterCallback(): void
    {
        Router::get('/home', ['HomeController', 'show']);

        Router::prefix('/v2', function () {
            Router::get('/items', ['ItemController', 'index']);
        });

        Router::get('/about', ['PageController', 'about']);

        $routes = Router::routes();

        $this->assertArrayHasKey('/home', $routes['GET']);
        $this->assertArrayHasKey('/v2/items', $routes['GET']);
        $this->assertArrayHasKey('/about', $routes['GET']);
    }

    public function testNestedPrefixesStack(): void
    {
        Router::prefix('/api', function () {
            Router::prefix('/v1', function () {
                Router::get('/users', ['UserController', 'index']);
            });
        });

        $routes = Router::routes();

        $this->assertArrayHasKey('/api/v1/users', $routes['GET']);
    }

    public function testMiddlewareGroupAppliesMiddleware(): void
    {
        Router::middleware(['auth'], function () {
            Router::get('/dashboard', ['DashboardController', 'index']);
        });

        $routes = Router::routes();

        $this->assertSame(['auth'], $routes['GET']['/dashboard']->middleware);
    }

    public function testMiddlewareGroupMergesWithRouteMiddleware(): void
    {
        Router::middleware(['auth'], function () {
            Router::get('/admin', ['AdminController', 'index'], ['throttle']);
        });

        $routes = Router::routes();

        $this->assertSame(['auth', 'throttle'], $routes['GET']['/admin']->middleware);
    }

    public function testMiddlewareGroupRestoresAfterCallback(): void
    {
        Router::get('/public', ['PageController', 'public']);

        Router::middleware(['auth'], function () {
            Router::get('/protected', ['PageController', 'protected']);
        });

        Router::get('/other-public', ['PageController', 'other']);

        $routes = Router::routes();

        $this->assertSame([], $routes['GET']['/public']->middleware);
        $this->assertSame(['auth'], $routes['GET']['/protected']->middleware);
        $this->assertSame([], $routes['GET']['/other-public']->middleware);
    }

    public function testRoutesReturnsAllRegisteredRoutes(): void
    {
        Router::get('/a', ['A', 'index']);
        Router::post('/b', ['B', 'store']);

        $routes = Router::routes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('POST', $routes);
        $this->assertCount(1, $routes['GET']);
        $this->assertCount(1, $routes['POST']);
    }

    public function testRouteByNameReturnsCorrectUri(): void
    {
        Router::get('/users/{id}', ['UserController', 'show'])->name('users.show');

        $url = Router::route('users.show', ['id' => '42']);

        $this->assertSame('/users/42', $url);
    }

    public function testRouteByNameReturnsEmptyStringWhenNotFound(): void
    {
        $url = Router::route('nonexistent');

        $this->assertSame('', $url);
    }

    public function testRouteByNameWithMultipleParameters(): void
    {
        Router::get('/orgs/{org}/repos/{repo}', ['RepoController', 'show'])->name('repos.show');

        $url = Router::route('repos.show', ['org' => 'acme', 'repo' => 'api']);

        $this->assertSame('/orgs/acme/repos/api', $url);
    }

    public function testCurrentReturnsNullWhenNoRouteMatched(): void
    {
        $this->assertNull(Router::current());
    }

    public function testGetWithClosureAction(): void
    {
        $route = Router::get('/hello', fn() => 'world');

        $this->assertInstanceOf(\Closure::class, $route->action);
    }
}
