# Helper Functions

ZeroPing ships with 30+ global helper functions defined in `app/Helpers/helpers.php`. They are loaded early in the bootstrap process, before the container is fully wired, so they are available everywhere. Every function guards itself with `function_exists()` so you can override any helper in userland code by defining it first.

## Path Helpers

### `base_path()`

```php
base_path(string $path = ''): string
```

Returns the application root directory, optionally joining a relative path.

```php
base_path();                    // /var/www/my-app
base_path('config/app.php');    // /var/www/my-app/config/app.php
```

### `storage_path()`

```php
storage_path(string $path = ''): string
```

Returns the `storage/` directory path.

```php
storage_path();                 // /var/www/my-app/storage
storage_path('logs/app.log');   // /var/www/my-app/storage/logs/app.log
```

### `database_path()`

```php
database_path(string $path = ''): string
```

Returns the `database/` directory path.

```php
database_path();                        // /var/www/my-app/database
database_path('migrations/001_init.sql'); // /var/www/my-app/database/migrations/001_init.sql
```

### `public_path()`

```php
public_path(string $path = ''): string
```

Returns the `public/` directory path.

```php
public_path();                  // /var/www/my-app/public
public_path('css/app.css');     // /var/www/my-app/public/css/app.css
```

## Application & Container

### `app()`

```php
app(string|null $abstract = null): mixed
```

Returns the application container or resolves a binding from it.

```php
$container = app();                         // Container instance
$mailer    = app(MailManager::class);       // Resolved MailManager
$logger    = app('logger');                 // Resolved by alias
```

### `class_basename()`

```php
class_basename(string|object $class): string
```

Returns the class name without its namespace.

```php
class_basename(App\Models\User::class); // 'User'
class_basename(new App\Models\Post());  // 'Post'
```

## Configuration & Environment

### `config()`

```php
config(string $key, mixed $default = null): mixed
```

Retrieves a configuration value using dot notation.

```php
$appName  = config('app.name');                     // 'ZeroPing App'
$debug    = config('app.debug', false);             // false
$mailHost = config('mail.mailers.smtp.host');
```

### `env()`

```php
env(string $key, mixed $default = null): mixed
```

Reads an environment variable. Checks `$_ENV`, `getenv()`, and `$_SERVER` in order.

```php
$dbHost   = env('DB_HOST', '127.0.0.1');
$appDebug = env('APP_DEBUG', false);
```

## HTTP & Routing

### `response()`

```php
response(mixed $content = null, int $status = 200, array $headers = []): ResponseFactory|Response
```

Called with no arguments, returns the `ResponseFactory`. Called with content, returns a `Response` directly.

```php
// JSON response
$res = response(json_encode(['ok' => true]), 200, ['Content-Type' => 'application/json']);

// Factory for fluent building
response()->make('Hello', 200);
```

### `redirect()`

```php
redirect(string $to, int $status = 302): Response
```

Creates a redirect response.

```php
redirect('/dashboard');
redirect('/login', 301);
```

### `route()`

```php
route(string $name, array $parameters = [], bool $absolute = true, ?int $expiration = null): string
```

Generates a URL for a named route.

```php
route('users.show', ['id' => 42]);
// https://example.com/users/42

route('users.show', ['id' => 42], absolute: false);
// /users/42

route('download.link', ['id' => 5], expiration: 3600);
// https://example.com/download/5?expires=1735000000
```

### `url()`

```php
url(?string $path = null): string
```

Generates an absolute URL. With no argument, returns the current request URL.

```php
url('/about');      // https://example.com/about
url();              // https://example.com/current/path?query=1
```

### `asset()`

```php
asset(string $path): string
```

Generates an absolute URL to a file in the `public/` directory.

```php
asset('css/app.css');   // https://example.com/css/app.css
asset('images/logo.png');
```

## Views

### `view()`

```php
view(string $view, array $data = []): string
```

Renders a view template and returns the HTML string.

```php
echo view('dashboard', ['user' => $user]);
echo view('emails/welcome', ['name' => 'Ada']);
```

## Localization

### `trans()`

```php
trans(string $key, array $replace = [], ?string $locale = null): string
```

Looks up a translation key from `resources/lang/{locale}/{file}.php`.

```php
trans('auth.failed');
trans('messages.welcome', ['name' => 'Ada']);       // "Welcome, Ada"
trans('validation.required', [], 'fr');             // French locale
```

### `__()`

Alias of `trans()`. Identical signature and behavior.

```php
__('auth.failed');
__('messages.greeting', ['name' => 'Ada']);
```

## Cache & Storage

### `cache()`

```php
cache(): mixed   // returns CacheManager
cache(string $key, mixed $default = null): mixed    // get
cache(array $keyValue, ?int $ttl = null): bool      // set
```

A multi-mode helper for the cache system.

```php
// Get the CacheManager instance
$manager = cache();

// Read a value
$value = cache('homepage.html');
$value = cache('user.42.profile', null);    // null default

// Write a value (TTL in seconds)
cache(['user.42.profile' => $profile], 300);
cache(['settings' => $settings]);           // no expiration
```

### `storage()`

```php
storage(?string $disk = null): FilesystemRepository
```

Returns a filesystem disk instance.

```php
$disk = storage();           // default disk
$disk = storage('public');   // public disk
$disk = storage('cache');    // cache disk

$disk->put('file.txt', 'contents');
$contents = $disk->get('file.txt');
```

## Queue & Jobs

### `dispatch()`

```php
dispatch(Job $job): void
```

Dispatches a job to the queue.

```php
use App\Jobs\ProcessInvoiceJob;

dispatch(new ProcessInvoiceJob($invoice));
```

## Validation

### `validator()`

```php
validator(array $data, array $rules): Validator
```

Creates a new `Validator` instance.

```php
$v = validator($_POST, [
    'name'  => 'required|min:2',
    'email' => 'required|email',
]);

if ($v->fails()) {
    $errors = $v->errors();
}
```

## Session & CSRF

### `session()`

```php
session(string|array|null $key = null, mixed $default = null): mixed
```

Multi-mode session helper.

```php
$session = session();                       // Session instance
$flash   = session('success');              // Get value
session(['success' => 'Profile updated!']); // Set value (returns null)
```

### `csrf_token()`

```php
csrf_token(): string
```

Returns the current CSRF token string.

```php
$token = csrf_token(); // "a1b2c3d4..."
```

### `csrf_field()`

```php
csrf_field(): string
```

Returns an HTML hidden input containing the CSRF token.

```php
<form method="POST" action="/settings">
    <?= csrf_field() ?>
</form>
```

## Miscellaneous

### `abort()`

```php
abort(int $code, string $message = ''): never
```

Throws an HTTP exception for the given status code.

```php
abort(404);
abort(403, 'Access denied.');
```

### `now()`

```php
now(string|\DateTimeZone|null $tz = null): \DateTimeImmutable
```

Returns the current date and time as an immutable object.

```php
$now = now();
$utc = now('UTC');
$bkk = now('Asia/Bangkok');

$formatted = now()->format('Y-m-d H:i:s');
```

### `old()`

```php
old(string $key, mixed $default = null): mixed
```

Retrieves a value flashed to the session by the previous request (useful for repopulating form fields after validation failure).

```php
<input type="text" name="email" value="<?= e(old('email')) ?>">
```

## Debugging

### `dump()`

```php
dump(mixed ...$args): void
```

Dumps one or more values to the output without halting execution.

```php
dump($user, $request, $_POST);
```

### `dd()`

```php
dd(mixed ...$args): never
```

Dumps values and terminates. Uses `exit(0)` so process managers are not confused.

```php
dd($user);
dd($a, $b, $c);
```

### `benchmark()`

```php
benchmark(callable $callback, int $iterations = 1): float
```

Benchmarks a callable over N iterations and returns the average execution time in seconds.

```php
$avg = benchmark(function () {
    // code to measure
}, 1000);

echo "Average: " . number_format($avg * 1000, 4) . " ms";
```

## Logging

### `logger()`

```php
logger(?string $message = null, array $context = []): mixed
```

Logs a debug message, or returns the `Log` instance when called with no arguments.

```php
logger('User logged in', ['user_id' => 42]);

// Get the logger to use other levels
logger()->warning('Disk almost full', ['disk' => 'local']);
logger()->error('Payment failed', ['order' => $orderId]);
```

## String Utilities

### `e()`

```php
e(mixed $value): string
```

Escapes a value for safe HTML output using `htmlspecialchars()` with `ENT_QUOTES | ENT_SUBSTITUTE`.

```php
echo e($user['name']);           // Escapes <, >, ", ', &
echo e('<script>alert(1)</script>'); // &lt;script&gt;alert(1)&lt;/script&gt;
```

### `str_plural()`

```php
str_plural(string $value, int $count = 2): string
```

Naive English pluralisation. Appends `s` unless `$count === 1`.

```php
str_plural('comment', 3);  // 'comments'
str_plural('comment', 1);  // 'comment'
str_plural('post', 0);     // 'posts'
```

### `str_singular()`

```php
str_singular(string $value): string
```

Naive English singularisation. Strips the trailing `s`.

```php
str_singular('comments'); // 'comment'
str_singular('posts');    // 'post'
```

## HTTP Client

### `http_client()`

```php
http_client(): HttpClient
```

Returns a pre-configured HTTP client instance for making outbound requests.

```php
$client   = http_client();
$response = $client->get('https://api.example.com/users');
$data     = json_decode($response->body(), true);
```

## Overriding Helpers

Because every helper is wrapped in `function_exists()`, you can override any of them by defining your own version before the helper file is loaded — for example, in a custom bootstrap file or a service provider:

```php
// app/Overrides/helpers.php – loaded before app/Helpers/helpers.php

function now(string|\DateTimeZone|null $tz = null): \DateTimeImmutable
{
    // Use a custom clock for testing
    return TestClock::now($tz);
}
```
