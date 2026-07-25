<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Routing\Route;

class RouteTest extends \Tests\TestCase
{
    public function testConstructorSetsProperties(): void
    {
        $route = new Route('GET', '/users', ['UserController', 'index'], ['auth']);

        $this->assertSame('GET', $route->method);
        $this->assertSame('/users', $route->uri);
        $this->assertSame(['UserController', 'index'], $route->action);
        $this->assertSame(['auth'], $route->middleware);
        $this->assertNull($route->name);
    }

    public function testNameReturnsSelfForFluentInterface(): void
    {
        $route = new Route('GET', '/', fn() => null, []);

        $result = $route->name('home');

        $this->assertSame($route, $result);
    }

    public function testNameSetsAndGetNameReturnsName(): void
    {
        $route = new Route('POST', '/submit', ['FormController', 'store'], []);

        $route->name('form.submit');

        $this->assertSame('form.submit', $route->getName());
    }

    public function testGetNameReturnsNullWhenNotSet(): void
    {
        $route = new Route('DELETE', '/items/{id}', ['ItemController', 'destroy'], []);

        $this->assertNull($route->getName());
    }

    public function testConstructorAcceptsClosureAction(): void
    {
        $action = fn() => 'hello';
        $route = new Route('GET', '/test', $action, []);

        $this->assertInstanceOf(\Closure::class, $route->action);
    }

    public function testConstructorAcceptsEmptyMiddlewareArray(): void
    {
        $route = new Route('GET', '/open', ['PageController', 'show'], []);

        $this->assertSame([], $route->middleware);
    }

    public function testConstructorAcceptsMultipleMiddleware(): void
    {
        $route = new Route('GET', '/admin', ['AdminController', 'index'], ['auth', 'admin', 'throttle']);

        $this->assertCount(3, $route->middleware);
        $this->assertSame(['auth', 'admin', 'throttle'], $route->middleware);
    }
}
