# Security

Security helpers live under `App\Core\Security`. The most common tasks — verifying passwords, encrypting data, issuing CSRF tokens, throttling, and signing URLs — are available as static helpers you can call directly from controllers.

## Hashing & password hashing

```php
use App\Core\Security\Hash;

$hash = Hash::make('secret');          // bcrypt
$ok   = Hash::check('secret', $hash);  // true
$re   = Hash::needsRehash($hash);      // re-hash if cost changed
```

## Encryption

```php
use App\Core\Security\Encryption;

$seal = Encryption::encrypt('token');    // AES-256-CBC
$open = Encryption::decrypt($seal);      // original string
```

The key comes from `config/security.php` → `security.key` (a separate value from `APP_KEY`).

## CSRF protection

```php
use App\Core\Security\CSRF;
use App\Core\Security\CSRFToken;

$token = CSRF::generate();          // store & return to client
$ok    = CSRF::check($token);       // hash_equals-safe compare

// In a form view:
echo CSRFToken::field();            // <input type="hidden" name="_token" value="...">
```

## Rate limiting

```php
use App\Core\Security\RateLimiter;

// Returns true if under the limit; false once exhausted
$allowed = RateLimiter::attempt('login:' . $ip, 5, 1); // 5 attempts / 1 minute
```

## Signed URLs

```php
use App\Core\Security\Signature;
use App\Core\Security\URLSigner;

$signed = Signature::sign('https://app.test/download?file=report');
$ok     = Signature::validate($signed);

// Or sign a named route:
$url = URLSigner::signedRoute('download', ['file' => 'report']);
$url = URLSigner::temporarySignedRoute('download', 3600, ['file' => 'report']);
```

## Password reset

```php
use App\Core\Security\Password;

$broker = Password::broker();
$status = $broker->sendResetLink(['email' => $email]); // 'passwords.sent'
$status = $broker->reset(
    ['email' => $email, 'token' => $token, 'password' => $pw],
    fn ($user, $password) => $user->update(['password' => Hash::make($password)])
);
```

## Random helpers

```php
use App\Core\Security\Random;

Random::string(32);   // random alphanumeric string
Random::uuid();       // UUID v4
Random::token(60);    // long random token
```

## Using the security middleware

The CSRF, throttling, and signed-URL middleware classes live in `App\Core\Security\Middleware`. They are *not* wired to route short-names by default: the router resolves a short name only to `App\Http\Middleware\{Name}Middleware`, and the base middleware `handle()` takes no arguments while these take `handle(\Closure $next)`. The simplest path is to call the static helpers directly in your controller, as shown above.

To use them as route middleware, create a thin wrapper under `app/Http/Middleware` that extends the core class, then reference its short name on the route:

```php
namespace App\Http\Middleware;

use App\Core\Security\Middleware\ThrottleRequests;

class ThrottleMiddleware extends ThrottleRequests {}
```

## Best Practices

Hash every password with `Hash::make()`, validate CSRF tokens on every state-changing (POST/PUT/DELETE) request, and throttle login/registration endpoints with `RateLimiter::attempt()`.

## Common Mistakes

Assuming `'csrf'`, `'throttle'`, or `'signed'` are usable route middleware short-names out of the box — they are not. Also, encryption keys off `security.key`, not `APP_KEY`.

## Notes

A failed CSRF check, throttle, or signature validation throws `App\Core\Security\Exceptions\SecurityException`. The config keys `hash_driver`, `rate_limits`, and `csrf_lifetime` are declared in `config/security.php` but currently unused — `Hash` always uses bcrypt and throttling reads its limits from the middleware call.

## Tips

Drop `CSRFToken::field()` into every HTML form so the token is submitted as `_token`, then verify it in your controller with `CSRF::check(Request::input('_token'))`.
