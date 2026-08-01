# Routing

The ZeroPing router maps HTTP requests to controller actions or closures using a clean, static API. Routes are defined in `config/routes.php` and loaded automatically on every request.

## Basic Routes

Use the `Router` class to register routes for any HTTP verb. The second argument is either a `[ControllerClass, 'method']` array or a `Closure`.

```php
use App\Core\Routing\Router;
use App\Controllers\PostController;

Router::get('/posts', [PostController::class, 'index']);
Router::post('/posts', [PostController::class, 'store']);
Router::put('/posts/{id}', [PostController::class, 'update']);
Router::patch('/posts/{id}', [PostController::class, 'patch']);
Router::delete('/posts/{id}', [PostController::class, 'destroy']);
```

Closures are useful for quick prototyping:

```php
Router::get('/ping', function () {
    echo 'pong';
});
```

To respond to multiple methods, use `Router::match()`:

```php
Router::match(['GET', 'POST'], '/contact', [ContactController::class, 'handle']);
```

To respond to every method, use `Router::any()`:

```php
Router::any('/webhook', [WebhookController::class, 'receive']);
```

## Route Parameters

Wrap a segment in curly braces to capture it as a parameter. Parameters are passed to the controller method in the order they appear in the URI.

```php
// Required parameter
Router::get('/users/{id}', [UserController::class, 'show']);

// Multiple parameters
Router::get('/posts/{post}/comments/{comment}', [CommentController::class, 'show']);
```

### Optional Parameters

Append `?` to make a parameter optional. The controller method should default the argument:

```php
Router::get('/archive/{year?}', [ArchiveController::class, 'index']);
```

```php
public function index(int $year = 0): void
{
    $year = $year ?: (int) date('Y');
    // ...
}
```

## Named Routes

Call `->name()` on the returned `Route` object to assign a name. Named routes allow you to generate URLs independently of the URI structure.

```php
Router::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
Router::post('/login', [AuthController::class, 'login'])->name('auth.login');
```

### The `route()` Helper

Generate a URL for a named route using the global `route()` helper:

```php
// Absolute URL: https://example.com/users/42
$url = route('users.show', ['id' => 42]);

// Relative URL
$url = route('users.show', ['id' => 42], absolute: false);

// With signed expiration (appends ?expires=<timestamp>)
$url = route('users.show', ['id' => 42], expiration: 3600);
```

You can also call `Router::route()` directly if you need the path without scheme/host:

```php
$path = Router::route('users.show', ['id' => 42]); // /users/42
```

## Route Groups

Groups let you share attributes — prefix, middleware, or both — across many routes without repeating yourself.

### Prefix Group

```php
Router::prefix('/admin', function () {
    Router::get('/dashboard', [AdminController::class, 'dashboard']);
    Router::get('/users', [AdminController::class, 'users']);
    // Routes resolve to /admin/dashboard and /admin/users
});
```

### Middleware Group

```php
Router::middleware(['auth'], function () {
    Router::get('/account', [AccountController::class, 'index']);
    Router::post('/account', [AccountController::class, 'update']);
});
```

### Combined Group

`Router::group()` accepts a single attributes array with both `prefix` and `middleware` keys:

```php
Router::group(['prefix' => '/api/v1', 'middleware' => ['auth', 'throttle']], function () {
    Router::get('/profile', [ProfileController::class, 'show']);
    Router::put('/profile', [ProfileController::class, 'update']);
    Router::delete('/account', [AccountController::class, 'destroy']);
});
```

Groups can be nested. Inner groups inherit the outer group's prefix and middleware:

```php
Router::group(['prefix' => '/admin', 'middleware' => ['auth']], function () {
    Router::get('/dashboard', [AdminController::class, 'dashboard']);

    Router::group(['prefix' => '/settings', 'middleware' => ['admin.role']], function () {
        Router::get('/', [SettingsController::class, 'index']);
        Router::post('/', [SettingsController::class, 'update']);
        // Resolves to /admin/settings with ['auth', 'admin.role'] middleware
    });
});
```

## Route Middleware Per Route

Apply middleware to a single route by passing it as the third argument:

```php
Router::get('/dashboard', [DashboardController::class, 'index'], ['auth']);
Router::post('/upload', [UploadController::class, 'store'], ['auth', 'csrf']);
```

## Viewing Registered Routes

List all registered routes in a formatted table:

```bash
php zero route:list
```

Output includes the HTTP method, URI, controller action, middleware, and name for every registered route.

## Route Caching

In production, cache the compiled route table to skip the regex compilation step on every request:

```bash
php zero route:cache
```

To clear the route cache:

```bash
php zero route:clear
```

**Important:** always re-run `php zero route:cache` after modifying `config/routes.php`. Serving stale cache is a common source of "route not found" bugs after deployment.

The cache file is written to `storage/cache/routes.php` and loaded automatically when it exists.

## Complete Routes File Example

```php
<?php

use App\Core\Routing\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PostController;
use App\Controllers\UserController;

// Public routes
Router::get('/', [PostController::class, 'index'])->name('home');
Router::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

// Authentication
Router::get('/login', [AuthController::class, 'showLogin'])->name('auth.login');
Router::post('/login', [AuthController::class, 'login']);
Router::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Authenticated area
Router::group(['prefix' => '/app', 'middleware' => ['auth']], function () {
    Router::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Router::group(['prefix' => '/users'], function () {
        Router::get('/', [UserController::class, 'index'])->name('users.index');
        Router::get('/{id}', [UserController::class, 'show'])->name('users.show');
        Router::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Router::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
```

## Common Mistakes

- **Forgetting to re-cache after editing routes.** Running `php zero route:cache` once and then modifying `config/routes.php` without re-caching means the application continues to serve the old cache. Always include `php zero route:cache` in your deployment pipeline.
- **Optional parameters on non-terminal segments.** Optional parameters (`{year?}`) should appear at the end of the URI. Placing them before required segments produces ambiguous patterns.
- **Using `->name()` inside a group.** Named routes work correctly inside groups — just remember the prefix is part of the URI, not the name.
