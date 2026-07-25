<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Core\Routing\Router;
use App\Core\Routing\Route;
use App\Core\Security\CSRF;
use App\Core\Session\Session;

class RouteDispatchTest extends \Tests\TestCase
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

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];

        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
    }

    public function testRouteNameResolution(): void
    {
        Router::get('/users', ['UserController', 'index'])->name('users.index');
        Router::get('/users/{id}', ['UserController', 'show'])->name('users.show');

        $url = Router::route('users.show', ['id' => '42']);

        $this->assertSame('/users/42', $url);
    }

    public function testRouteNameResolutionWithNoParameters(): void
    {
        Router::get('/', ['HomeController', 'index'])->name('home');

        $url = Router::route('home');

        $this->assertSame('/', $url);
    }

    public function testRouteNameReturnsEmptyStringForInvalidName(): void
    {
        Router::get('/test', ['TestController', 'index']);

        $url = Router::route('nonexistent');

        $this->assertSame('', $url);
    }

    public function testUniqueRouteNamesDontConflict(): void
    {
        Router::get('/a', ['A', 'index'])->name('a');
        Router::get('/b', ['B', 'index'])->name('b');

        $this->assertSame('/a', Router::route('a'));
        $this->assertSame('/b', Router::route('b'));
    }

    public function testGetRouteRegisteredCorrectly(): void
    {
        Router::get('/posts', ['PostController', 'index']);

        $routes = Router::routes();

        $this->assertArrayHasKey('GET', $routes);
        $this->assertArrayHasKey('/posts', $routes['GET']);
        $this->assertSame(['PostController', 'index'], $routes['GET']['/posts']->action);
    }

    public function testPostRouteRegisteredCorrectly(): void
    {
        Router::post('/posts', ['PostController', 'store']);

        $routes = Router::routes();

        $this->assertArrayHasKey('POST', $routes);
        $this->assertArrayHasKey('/posts', $routes['POST']);
    }

    public function testRoutesAreIndependentByMethod(): void
    {
        Router::get('/posts', ['PostController', 'index']);
        Router::post('/posts', ['PostController', 'store']);

        $routes = Router::routes();

        $this->assertSame(['PostController', 'index'], $routes['GET']['/posts']->action);
        $this->assertSame(['PostController', 'store'], $routes['POST']['/posts']->action);
    }

    public function testDispatchWithClosureActionSucceeds(): void
    {
        $executed = false;

        Router::get('/hello', function () use (&$executed) {
            $executed = true;
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/hello';

        Router::dispatch(__DIR__ . '/../../');

        $this->assertTrue($executed);
    }

    public function testDynamicRouteMatching(): void
    {
        $params = [];

        Router::get('/users/{id}', function (...$args) use (&$params) {
            $params = $args;
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users/99';

        Router::dispatch(__DIR__ . '/../../');

        $this->assertSame(['99'], $params);
    }

    public function testMultipleDynamicParameters(): void
    {
        $params = [];

        Router::get('/orgs/{org}/repos/{repo}', function (...$args) use (&$params) {
            $params = $args;
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/orgs/acme/repos/api';

        Router::dispatch(__DIR__ . '/../../');

        $this->assertSame(['acme', 'api'], $params);
    }

    public function testStaticRouteTakesPriorityOverDynamicRoute(): void
    {
        $hit = '';

        Router::get('/users/create', function () use (&$hit) {
            $hit = 'static';
        });

        Router::get('/users/{id}', function () use (&$hit) {
            $hit = 'dynamic';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/users/create';

        Router::dispatch(__DIR__ . '/../../');

        $this->assertSame('static', $hit);
    }

    public function testRouteWithTrailingSlashNormalizes(): void
    {
        $hit = false;

        Router::get('/about', function () use (&$hit) {
            $hit = true;
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/about/';

        Router::dispatch(__DIR__ . '/../../');

        $this->assertTrue($hit);
    }

    public function testCurrentRouteIsSetAfterDispatch(): void
    {
        Router::get('/current', function () {
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/current';

        Router::dispatch(__DIR__ . '/../../');

        $current = Router::current();

        $this->assertNotNull($current);
        $this->assertSame('/current', $current->uri);
    }
}
