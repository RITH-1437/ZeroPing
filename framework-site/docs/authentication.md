# Authentication

ZeroPing ships with session-based authentication out of the box. The `AuthManager` class provides a static facade over `SessionGuard` for login, logout, and user retrieval, while `PasswordHasher` handles secure password storage and `PasswordBroker` manages password-reset flows.

## How It Works

Authentication state is stored in the PHP session. On login, the user's data array is written to the session after the session ID is regenerated (to prevent session fixation). On logout, the session is destroyed entirely.

```txt
POST /login → AuthManager::login($user) → Session stores user data
GET  /dashboard → AuthManager::check() → true → proceed
POST /logout → AuthManager::logout() → Session destroyed
```

## The AuthManager

The `AuthManager` class (`App\Core\Auth\AuthManager`) is the primary interface for authentication state.

```php
use App\Core\Auth\AuthManager;

// Log a user in (pass the user data array from your database)
AuthManager::login($userArray);

// Log the current user out
AuthManager::logout();

// Check if a user is authenticated
if (AuthManager::check()) { /* ... */ }

// Check if the session is unauthenticated (guest)
if (AuthManager::guest()) { /* ... */ }

// Retrieve the full user array
$user = AuthManager::user(); // array|null

// Retrieve the authenticated user's ID
$id = AuthManager::id(); // int|null
```

## Protecting Routes with AuthMiddleware

Apply the built-in `auth` middleware to any route that requires authentication. Unauthenticated visitors are automatically redirected to `/login`:

```php
use App\Core\Routing\Router;

// Single route
Router::get('/dashboard', [DashboardController::class, 'index'], ['auth']);

// Group of routes
Router::group(['prefix' => '/account', 'middleware' => ['auth']], function () {
    Router::get('/', [AccountController::class, 'index']);
    Router::put('/', [AccountController::class, 'update']);
    Router::delete('/', [AccountController::class, 'destroy']);
});
```

Redirect authenticated users away from login/register pages with the `guest` middleware:

```php
Router::get('/login', [AuthController::class, 'showLogin'], ['guest']);
Router::get('/register', [AuthController::class, 'showRegister'], ['guest']);
```

## Password Hashing

Use `PasswordHasher` to hash passwords before storing them, and to verify them on login. It wraps PHP's `password_hash()` / `password_verify()` with the bcrypt algorithm.

```php
use App\Core\Auth\PasswordHasher;

// Hash before inserting into the database
$hashed = PasswordHasher::hash($plainTextPassword);

// Verify on login
if (!PasswordHasher::verify($plainTextPassword, $hashedFromDb)) {
    // Wrong password
}

// Check if a hash needs rehashing (e.g. after a cost increase)
if (PasswordHasher::needsRehash($hash)) {
    $newHash = PasswordHasher::hash($plainTextPassword);
    // Update hash in database
}
```

## Password Reset Flow

`PasswordBroker` manages the token-based password reset flow. It generates signed tokens, stores them in the database via `DatabaseTokenRepository`, and validates them on submission.

```php
use App\Core\Security\PasswordBroker;

// 1. Send a reset link
$broker = app(PasswordBroker::class);
$token  = $broker->createToken($user['email']);
// Mail the token in a link: /password/reset?token={$token}&email={$user['email']}

// 2. Validate and reset
$result = $broker->reset(
    email: $request['email'],
    token: $request['token'],
    password: $request['password'],
);

if ($result === PasswordBroker::PASSWORD_RESET) {
    // Success — redirect to login
} else {
    // Invalid token or email
}
```

## Scaffolding with `make:auth`

Run the scaffolding command to generate login, register, and password-reset controllers, views, and routes in one step:

```bash
php zero make:auth
```

This creates:
- `app/Controllers/Auth/LoginController.php`
- `app/Controllers/Auth/RegisterController.php`
- `app/Controllers/Auth/PasswordResetController.php`
- Corresponding views in `views/auth/`
- Route entries in `config/routes.php`

## Example Login Controller

```php
<?php

namespace App\Controllers\Auth;

use App\Core\Auth\AuthManager;
use App\Core\Auth\PasswordHasher;
use App\Core\View\Controller;
use App\Models\User;

class LoginController extends Controller
{
    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = User::where('email', $email)->first();

        if ($user === null || !PasswordHasher::verify($password, $user['password'])) {
            session(['_errors' => ['credentials' => 'These credentials do not match our records.']]);
            redirect('/login');
            return;
        }

        AuthManager::login($user);

        redirect('/dashboard');
    }

    public function logout(): void
    {
        AuthManager::logout();
        redirect('/login');
    }
}
```

The corresponding login form:

```php
<form method="POST" action="/login">
    <?= csrf_field() ?>

    <input type="email" name="email" value="<?= e(old('email')) ?>">
    <input type="password" name="password">

    <button type="submit">Sign In</button>
</form>
```

## Accessing the Authenticated User in Views

Pass the authenticated user to your views from the controller:

```php
public function dashboard(): void
{
    $this->view('dashboard', [
        'user' => AuthManager::user(),
    ]);
}
```

In the view:

```php
<p>Welcome back, <?= e($user['name']) ?></p>
```

## Auth Configuration

Authentication settings live in `config/auth.php`. You can configure the guard, session key, and token TTL:

```php
return [
    'guard'       => 'session',
    'session_key' => 'auth_user',
    'token_ttl'   => 60, // minutes
];
```

## Tips

- Always call `AuthManager::login()` rather than writing directly to `$_SESSION`. It regenerates the session ID, which is essential for preventing session-fixation attacks.
- Store only the fields you actually need in the session (e.g. id, name, email, role). Avoid storing large blobs or sensitive fields.
- Use `AuthManager::id()` in queries rather than fetching the full user array when you only need the primary key.
