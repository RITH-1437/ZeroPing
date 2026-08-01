# Middleware

Middleware sits between the HTTP request and your controller, giving you a composable place to inspect or modify the request, enforce access controls, set headers, or terminate the request early. ZeroPing middleware follows a simple, single-method contract that is easy to understand and test.

## How Middleware Works

When a route is matched, the router resolves the middleware list assigned to that route (and any enclosing groups), instantiates each class in order, and calls `handle()`. A void return means "proceed"; throwing an exception or redirecting terminates the pipeline before the controller is reached.

```txt
Request → Middleware 1 → Middleware 2 → Controller → Response
```

## Creating Middleware

Generate a new middleware class with the CLI:

```bash
php zero make:middleware EnsureEmailVerified
```

This creates `app/Http/Middleware/EnsureEmailVerifiedMiddleware.php`. Every middleware extends the abstract `Middleware` base and implements `handle()`:

```php
<?php

namespace App\Http\Middleware;

use App\Core\Auth\AuthManager;

class EnsureEmailVerifiedMiddleware extends Middleware
{
    public function handle(): void
    {
        $user = AuthManager::user();

        if ($user === null || empty($user['email_verified_at'])) {
            redirect('/verify-email');
            exit;
        }
    }
}
```

The `handle()` method receives no arguments. Access the current request state through superglobals (`$_GET`, `$_POST`, `$_SERVER`) or through injected services.

## Registering Middleware

### Per-Route Middleware

Pass an array of short names or FQCNs as the third argument to any route registration call:

```php
use App\Core\Routing\Router;
use App\Controllers\AccountController;

Router::get('/account', [AccountController::class, 'index'], ['auth']);
Router::post('/account', [AccountController::class, 'update'], ['auth', 'csrf']);
```

Short names resolve to `App\Http\Middleware\{Name}Middleware` automatically (e.g. `auth` → `AuthMiddleware`).

### Group Middleware

Apply middleware to a set of routes using `Router::middleware()` or `Router::group()`:

```php
Router::group(['prefix' => '/admin', 'middleware' => ['auth', 'admin.role']], function () {
    Router::get('/dashboard', [AdminController::class, 'dashboard']);
    Router::get('/users', [AdminController::class, 'users']);
});
```

### Global Middleware

To run middleware on every request, add it to the `web` middleware group in your bootstrap or routes file, or register it directly in the kernel before routes are loaded.

## Built-in Middleware

ZeroPing ships with the following ready-to-use middleware classes located in `app/Http/Middleware/`:

### `auth` — AuthMiddleware

Redirects unauthenticated visitors to `/login`. Use this on any route that requires a signed-in user.

```php
Router::get('/profile', [ProfileController::class, 'show'], ['auth']);
```

### `guest` — GuestMiddleware

Redirects already-authenticated users away from guest-only pages (e.g. login, register). Prevents authenticated users from accessing those forms.

```php
Router::get('/login', [AuthController::class, 'showLogin'], ['guest']);
Router::get('/register', [AuthController::class, 'showRegister'], ['guest']);
```

### `csrf` — VerifyCsrfToken

Verifies the CSRF token on mutating requests (`POST`, `PUT`, `PATCH`, `DELETE`). Throws a `SecurityException` on token mismatch.

```php
Router::post('/settings', [SettingsController::class, 'update'], ['auth', 'csrf']);
```

Include the CSRF field in every HTML form:

```php
<form method="POST" action="/settings">
    <?= csrf_field() ?>
    <!-- ... -->
</form>
```

### `cors` — Cors

Adds Cross-Origin Resource Sharing headers to responses. Configure allowed origins, methods, and headers in `config/cors.php`.

```php
Router::group(['prefix' => '/api', 'middleware' => ['cors']], function () {
    Router::get('/status', [ApiController::class, 'status']);
});
```

### `security` — SecurityHeaders

Injects security-related HTTP headers (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, etc.) on every response.

### `throttle` — ThrottleRequests

Rate-limits requests per IP. Accepts an optional `rate:max,window` parameter (e.g. `throttle:60,1` = 60 requests per minute).

```php
Router::post('/api/tokens', [TokenController::class, 'create'], ['throttle:10,1']);
```

## Returning Early from Middleware

Terminate the request inside `handle()` by either throwing an exception or redirecting and calling `exit`:

```php
public function handle(): void
{
    if (!$this->isAllowed()) {
        // Redirect early
        header('Location: /403');
        exit;
    }
}
```

Or use the `abort()` helper to throw an HTTP exception:

```php
public function handle(): void
{
    if (AuthManager::guest()) {
        abort(403, 'You must be logged in.');
    }
}
```

## Passing Data to Controllers

Middleware cannot directly inject values into controller method arguments. The recommended patterns are:

**Session / request attributes:** store data in `$_REQUEST` or the session:

```php
public function handle(): void
{
    $team = Team::findByUser(AuthManager::id());
    $_REQUEST['_team'] = $team;
}
```

**Container bindings:** bind a computed value into the service container so the controller can resolve it:

```php
public function handle(): void
{
    $locale = $this->resolveLocale();
    app()->instance('request.locale', $locale);
}
```

## Example: Rate-Limiting Middleware

```php
<?php

namespace App\Http\Middleware;

class RateLimitApiMiddleware extends Middleware
{
    private const MAX_REQUESTS = 100;
    private const WINDOW       = 60; // seconds

    public function handle(): void
    {
        $ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $key = 'rl:' . md5($ip);

        $hits = (int) cache($key, 0);

        if ($hits >= self::MAX_REQUESTS) {
            http_response_code(429);
            header('Retry-After: ' . self::WINDOW);
            echo json_encode(['error' => 'Too Many Requests']);
            exit;
        }

        cache([$key => $hits + 1], self::WINDOW);
    }
}
```

Register it in your routes:

```php
Router::group(['prefix' => '/api', 'middleware' => ['rate-limit-api']], function () {
    Router::get('/users', [ApiUserController::class, 'index']);
});
```

## Tips

- Keep middleware focused on a single concern. Chaining small middleware is easier to test and reuse than one large class.
- The middleware short-name resolver strips a trailing `Middleware` suffix when looking up the class, so both `auth` and `AuthMiddleware` resolve to `AuthMiddleware`.
- Middleware order matters. Place authentication checks before authorization checks.
