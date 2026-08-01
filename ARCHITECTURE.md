# ZeroPing Framework — Architecture Guide

> **Version:** 2.0.x | **Audience:** Framework contributors and advanced integrators

This document describes the internal architecture of the ZeroPing PHP framework. It is intended for developers who want to contribute to the core, write packages, or build a deep understanding of how the framework operates at runtime.

---

## Table of Contents

1. [Philosophy](#1-philosophy)
2. [Directory Structure](#2-directory-structure)
3. [Bootstrap Process](#3-bootstrap-process)
4. [Dependency Injection Container](#4-dependency-injection-container)
5. [Service Providers](#5-service-providers)
6. [HTTP Layer](#6-http-layer)
7. [Routing](#7-routing)
8. [ORM / Database](#8-orm--database)
9. [Validation](#9-validation)
10. [Console / CLI](#10-console--cli)
11. [Cache](#11-cache)
12. [Queue & Scheduler](#12-queue--scheduler)
13. [Testing](#13-testing)
14. [Package Architecture](#14-package-architecture)
15. [Contributing to Core](#15-contributing-to-core)

---

## 1. Philosophy

ZeroPing is built around three principles:

| Principle | Meaning |
|---|---|
| **Zero runtime dependencies** | The framework ships no third-party Composer packages in its critical path. Every subsystem is implemented from scratch. |
| **Batteries included** | Routing, ORM, validation, cache, queues, mail, events, scheduling, and a CLI ship in the box. No hunting for packages for common tasks. |
| **MVC + DI + ORM** | A clean MVC request/response cycle, a full-featured DI container with auto-wiring, and an ActiveRecord ORM form the foundation every feature is built on. |

These constraints keep the framework predictable, fast, and auditable. There is no magic you cannot trace to a source file.

---

## 2. Directory Structure

```
zero-ping/
├── app/
│   ├── Controllers/        # Application HTTP controllers
│   ├── Core/               # ★ ALL framework internals live here
│   │   ├── Application/    #   App bootstrap class (App.php)
│   │   ├── Auth/           #   Authentication guards & managers
│   │   ├── Cache/          #   CacheManager, CacheRepository, drivers
│   │   ├── Config/         #   Config, ConfigRepository, Env loader
│   │   ├── Console/        #   Command base, Console dispatcher, generators
│   │   ├── Container/      #   DI Container with auto-wiring
│   │   ├── Database/       #   QueryBuilder, Model, Schema, migrations
│   │   ├── Events/         #   EventDispatcher, Listener base
│   │   ├── Exceptions/     #   Framework exception types
│   │   ├── Filesystem/     #   Filesystem abstraction
│   │   ├── Http/           #   Kernel, Request, Response, ResponseFactory
│   │   ├── Localization/   #   Translator, locale loading
│   │   ├── Logging/        #   Logger, log drivers
│   │   ├── Mail/           #   Mailer, transports, Mailable base
│   │   ├── Notifications/  #   Notification dispatcher, channels
│   │   ├── ORM/            #   Legacy ORM (use Database\Model instead)
│   │   ├── Queue/          #   Job, Worker, QueueManager, drivers
│   │   ├── Routing/        #   Router, Route
│   │   ├── Scheduling/     #   Schedule, ScheduleManager, cron events
│   │   ├── Security/       #   CSRF, encryption, hashing
│   │   ├── Session/        #   Session manager
│   │   ├── Support/        #   Shared utilities (Str, Arr, Collection…)
│   │   ├── Testing/        #   TestResponse, HTTP testing helpers
│   │   └── Validation/     #   Validator, FluentValidator, FormRequest, rules
│   ├── Helpers/            # Global helper functions (helpers.php)
│   ├── Http/               # App-level Kernel, middleware, FormRequests
│   ├── Models/             # Application Eloquent-style models
│   ├── Providers/          # Application service providers
│   └── Services/           # Application service layer
├── bootstrap/
│   ├── app.php             # Creates the App instance (entry point for all boot paths)
│   └── cache/              # Compiled config + package manifest cache
├── config/                 # app.php, database.php, cache.php, queue.php, …
├── database/
│   ├── migrations/         # Timestamped migration files
│   └── seeders/            # Database seeders
├── packages/               # First-party & third-party ZeroPing packages
│   └── zeroping/
│       ├── support/        # Base package (ServiceProvider, CommandRegistry…)
│       └── queue/          # Reference package implementation
├── public/
│   └── index.php           # Web entry point — the only file exposed to the web
├── resources/
│   ├── views/              # PHP view templates
│   └── lang/               # Locale translation files
├── storage/                # Logs, cache files, compiled views, uploaded files
├── tests/
│   ├── bootstrap.php       # PHPUnit bootstrap
│   ├── TestCase.php        # Base test case
│   ├── Feature/            # HTTP / integration tests
│   └── Unit/               # Pure unit tests
└── zero                    # CLI entry point: php zero <command>
```

---

## 3. Bootstrap Process

Every web request and CLI invocation follows the same boot sequence:

```
public/index.php
    │
    ├─ define ZERO_PING_START, BASE_PATH
    ├─ require vendor/autoload.php
    ├─ require app/Helpers/helpers.php
    ├─ $app = require bootstrap/app.php          ← new App(basePath)
    │       │
    │       └─ App::__construct()
    │               ├─ new Container()           ← global DI container created
    │               └─ $this->bootstrap()
    │                       ├─ View::setBasePath()
    │                       ├─ session_start()   (if not CLI)
    │                       ├─ Env::load(.env)   (if APP_NAME not set)
    │                       ├─ loadConfig()      ← config/*.php → ConfigRepository
    │                       └─ registerProviders()
    │                               ├─ collectProviderClasses()  (app + packages)
    │                               ├─ foreach provider:
    │                               │       new $provider(container)
    │                               │       $provider->register()     ← bind into container
    │                               ├─ deferred providers → resolving() callbacks
    │                               ├─ foreach eager provider:
    │                               │       $provider->boot()         ← side effects ok here
    │                               └─ wire listens() + schedules()
    │
    └─ $app->handle(Request::capture())
            │
            └─ new Kernel($app)
                    └─ Kernel::handle()
                            ├─ bootstrap()       ← override hook
                            ├─ sortMiddleware()
                            ├─ foreach global middleware: callMiddleware()
                            └─ Router::dispatch(basePath)
                                    ├─ normalize URI
                                    ├─ require config/routes.php
                                    ├─ match route (static O(1) → dynamic regex)
                                    ├─ expand middleware groups
                                    ├─ foreach route middleware: handle()
                                    └─ call action (Closure or Controller@method)
                                            └─ send Response
```

### Config caching

In production (`APP_ENV=production`), `App::loadConfig()` loads `bootstrap/cache/config.php` **without** mtime validation, making config reads a single `require`. Run `php zero config:cache` to regenerate it.

### Package manifest

Package auto-discovery reads `bootstrap/cache/packages.php`. Regenerated by `php zero package:discover` or automatically when the cache is missing.

---

## 4. Dependency Injection Container

**File:** `app/Core/Container/Container.php`

The container is the backbone of the entire framework. Every service — from the router to the mailer — is resolved through it.

### Binding types

```php
// Transient: new instance on every make()
$container->bind(LoggerInterface::class, FileLogger::class);

// Singleton: same instance returned after first resolution
$container->singleton(DatabaseManager::class, DatabaseManager::class);

// Pre-built instance
$container->instance(Config::class, $config);
```

### Auto-wiring

When `make()` is called for a class with no explicit binding, the container uses reflection to inspect the constructor and recursively resolves all typed parameters:

```php
class UserController
{
    public function __construct(
        private UserRepository $users,  // auto-resolved
        private Mailer $mailer          // auto-resolved
    ) {}
}

$controller = $container->make(UserController::class); // works with no binding
```

### Interface convention discovery

If no binding exists for an interface, the container strips the `Interface` suffix and checks whether a concrete class exists:

```
App\Contracts\LoggerInterface  →  App\Contracts\Logger  (if class exists)
```

### Contextual bindings

Different consumers can receive different implementations of the same interface:

```php
$container->when(ReportMailer::class)
    ->needs(LoggerInterface::class)
    ->give(FileLogger::class);

$container->when(ApiController::class)
    ->needs(LoggerInterface::class)
    ->give(NullLogger::class);
```

### Resolving callbacks

```php
// Fire after every resolution of CacheRepository
$container->resolving(CacheRepository::class, function ($cache, $container) {
    $cache->setPrefix(config('cache.prefix'));
});

// Fire after any resolution
$container->resolving('*', function ($object, $container) { ... });
```

Deferred service providers use this mechanism: when a deferred service is first resolved, its provider is booted on demand.

### Performance

Two static caches avoid repeated reflection:

- `Container::$reflectionCache` — stores `ReflectionClass` instances per class.
- `Container::$parameterCache` — stores pre-computed constructor parameter metadata.

Both are keyed by class name and persist for the lifetime of the PHP process.

### Testing utilities

```php
$container->forgetInstance(DatabaseManager::class); // reset a singleton
$container->flush();                                 // reset entire container
Container::clearReflectionCache();                   // wipe static reflection cache
```

---

## 5. Service Providers

**Files:** `app/Providers/ServiceProvider.php`, `app/Providers/*.php`

Service providers are the canonical place to configure and wire framework services.

### Two-phase boot

```
Phase 1 — register()   All providers' register() methods run first.
                        Only bind things into the container here.
                        Do NOT call other services — they may not be ready.

Phase 2 — boot()       All register() calls have completed.
                        Safe to use other services, register routes,
                        subscribe to events, wire up the scheduler.
```

### Writing a service provider

```php
<?php

namespace App\Providers;

use App\Core\Scheduling\Schedule;

class SearchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(
            \App\Contracts\SearchInterface::class,
            \App\Services\MeilisearchClient::class
        );
    }

    public function boot(): void
    {
        // safe to use the container here
        $search = $this->container->make(\App\Contracts\SearchInterface::class);
        $search->setIndex(config('search.index'));
    }

    // Optional: declare scheduled tasks
    public function schedules(Schedule $schedule): void
    {
        $schedule->command('search:reindex')->daily();
    }

    // Optional: declare event listeners
    public function listens(): array
    {
        return [
            \App\Events\PostPublished::class => \App\Listeners\IndexPost::class,
        ];
    }
}
```

Register in `config/app.php`:

```php
'providers' => [
    App\Providers\AppServiceProvider::class,
    App\Providers\SearchServiceProvider::class,
],
```

### Deferred providers

Set `isDeferred()` to `true` and declare `provides()`. The provider is not registered until one of its services is first resolved — ideal for heavy services that are not needed on every request:

```php
public function isDeferred(): bool { return true; }

public function provides(): array
{
    return [\App\Contracts\PdfGeneratorInterface::class];
}
```

---

## 6. HTTP Layer

**Files:** `app/Core/Http/Kernel.php`, `app/Core/Http/Request.php`, `app/Core/Http/Response.php`

### Request lifecycle

```
1. Request::capture()      Wraps PHP superglobals ($_GET, $_POST, $_SERVER, …)
2. Kernel::handle()        Runs global middleware pipeline
3. Router::dispatch()      Matches route, runs route middleware, calls action
4. Action returns Response Kernel sends headers + body to client
```

### Middleware pipeline

Global middleware is declared in `app/Http/Kernel.php`:

```php
protected array $middleware = [
    \App\Http\Middleware\TrimStringsMiddleware::class,
    \App\Http\Middleware\ValidateCsrfTokenMiddleware::class,
];

protected array $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\StartSessionMiddleware::class,
    ],
    'api' => [
        \App\Http\Middleware\ThrottleRequestsMiddleware::class,
    ],
];

protected array $middlewarePriority = [
    \App\Http\Middleware\StartSessionMiddleware::class,
    \App\Http\Middleware\ValidateCsrfTokenMiddleware::class,
];
```

Global middleware runs before routing. Route and group middleware runs inside the router after a route is matched. Priority ordering is enforced within each phase.

### Writing middleware

```php
<?php

namespace App\Http\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!auth()->check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthenticated']);
            exit;
        }
    }
}
```

### Response

```php
// In a controller action:
return response()->json(['user' => $user]);       // JSON response
return response()->view('home', ['data' => $d]);  // HTML view response
return response()->redirect('/login');            // redirect
return response()->noContent();                   // 204
```

---

## 7. Routing

**Files:** `app/Core/Routing/Router.php`, `app/Core/Routing/Route.php`

Routes are defined in `config/routes.php` using a static fluent API.

### Route registration

```php
use App\Core\Routing\Router;

Router::get('/users', [UserController::class, 'index']);
Router::post('/users', [UserController::class, 'store']);
Router::get('/users/{id}', [UserController::class, 'show']);
Router::delete('/users/{id}', [UserController::class, 'destroy']);

// Closure route
Router::get('/ping', fn() => response()->json(['status' => 'ok']));

// Any method
Router::any('/webhook', [WebhookController::class, 'receive']);
```

### Route groups

```php
Router::prefix('/api/v1')
    ->middleware(['api', 'auth'])
    ->group(function () {
        Router::get('/profile', [ProfileController::class, 'show']);
        Router::put('/profile', [ProfileController::class, 'update']);
    });
```

### Named routes

```php
Router::get('/login', [AuthController::class, 'showLogin'])->name('login');

// In views/code:
$url = Router::route('login'); // '/login'
```

### Dispatch flow

```
Router::dispatch(basePath)
    │
    ├─ Normalize URI (strip query string, trailing slash)
    ├─ Load config/routes.php
    ├─ O(1) exact match in $staticRoutes
    │
    └─ If no exact match:
        ├─ Iterate $dynamicRoutes for the HTTP method
        ├─ Compile {param} placeholders → regex (cached in $compiledPatterns)
        ├─ preg_match() and extract named captures
        └─ Route found → expand middleware groups
                       → resolve short names to FQCNs (App\Http\Middleware\*Middleware)
                       → callMiddleware() for each
                       → Container::make(ControllerClass)->method(...$params)
                       → send Response
```

### Route parameters

```php
Router::get('/posts/{slug}/comments/{id}', function (string $slug, int $id) {
    return response()->json(Comment::where('post_slug', $slug)->find($id));
});
```

---

## 8. ORM / Database

**Files:** `app/Core/Database/Model.php`, `app/Core/Database/QueryBuilder.php`, `app/Core/Database/Schema.php`

> **Note:** `app/Core/ORM/Model.php` is the **legacy** base model and is deprecated. All new models should extend `App\Core\Database\Model`.

### Defining a model

```php
<?php

namespace App\Models;

use App\Core\Database\Model;

class Post extends Model
{
    protected string $table = 'posts';

    protected array $fillable = ['title', 'body', 'user_id', 'published'];

    protected array $casts = [
        'published'  => 'bool',
        'created_at' => 'datetime',
    ];
}
```

### Query builder

The `Model` base class proxies static method calls to a `QueryBuilder` instance bound to the model's table:

```php
// All records
$posts = Post::all();

// Filtered
$posts = Post::where('published', true)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();

// Single record
$post = Post::find(1);
$post = Post::where('slug', 'hello-world')->first();

// Aggregates
$count = Post::where('published', true)->count();

// Pagination
$paginator = Post::latest()->paginate(15);
```

### Create, update, delete

```php
// Mass create
$post = Post::create(['title' => 'Hello', 'body' => '...']);

// Update
$post->title = 'Updated';
$post->save();

// Or fluently
Post::where('id', 1)->update(['title' => 'New Title']);

// Delete
$post->delete();
Post::where('published', false)->delete();
```

### Soft deletes

Add the `SoftDeletes` concern and a `deleted_at` column. Soft-deleted records are excluded from all queries by default:

```php
use App\Core\ORM\Concerns\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
}

Post::find(1)->delete();              // sets deleted_at
Post::withTrashed()->get();           // includes deleted
Post::find(1)->restore();             // clears deleted_at
Post::find(1)->forceDelete();         // permanent
```

### Relationships

```php
class User extends Model
{
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
}

class Post extends Model
{
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'post_tag', 'post_id', 'tag_id');
    }
}

// Usage
$user->posts;            // Collection of Post
$post->author;           // User
$post->tags;             // Collection of Tag
```

### Migrations

```php
<?php

use App\Core\Database\Migration;
use App\Core\Database\Schema;
use App\Core\Database\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body')->nullable();
            $table->boolean('published')->default(false);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
```

Run migrations: `php zero migrate`
Roll back: `php zero migrate:rollback`
Refresh: `php zero migrate:fresh`

---

## 9. Validation

**Files:** `app/Core/Validation/Validator.php`, `app/Core/Validation/FluentValidator.php`, `app/Core/Validation/FormRequest.php`, `app/Core/Validation/RuleRegistry.php`

### String-based validation

```php
$result = Validator::make($_POST, [
    'email'    => 'required|email',
    'password' => 'required|min:8|confirmed',
    'age'      => 'required|integer|min:18',
]);

if ($result->fails()) {
    return response()->json(['errors' => $result->errors()], 422);
}
```

### Fluent validator

```php
$result = FluentValidator::make($data)
    ->field('email')->required()->email()->end()
    ->field('name')->required()->string()->max(255)->end()
    ->field('role')->in(['admin', 'editor', 'viewer'])->end()
    ->validate();
```

### FormRequest

Encapsulate authorization and validation inside a dedicated class:

```php
<?php

namespace App\Http\Requests;

use App\Core\Validation\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'     => 'required|string|max:255',
            'body'      => 'required|string',
            'published' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'A post title is required.',
        ];
    }
}
```

Inject it directly into a controller action; the framework validates automatically before the method runs:

```php
public function store(CreatePostRequest $request): Response
{
    $post = Post::create($request->validated());
    return response()->json($post, 201);
}
```

### Rule registry

Custom rules are registered once in a service provider:

```php
// In AppServiceProvider::boot()
RuleRegistry::register('slug', function (string $value): bool {
    return (bool) preg_match('/^[a-z0-9-]+$/', $value);
}, 'The :field must be a valid slug.');
```

---

## 10. Console / CLI

**Files:** `app/Core/Console/Console.php`, `app/Core/Console/Command.php`, `app/Core/Console/CommandRegistry.php`

### Entry point

```bash
php zero <command> [arguments] [--options]
```

`zero` is a PHP script that boots the application and delegates to `Console::dispatch()`.

### Built-in commands

| Command | Description |
|---|---|
| `php zero make:model Name` | Scaffold a model |
| `php zero make:controller Name` | Scaffold a controller |
| `php zero make:migration create_x` | Create a migration file |
| `php zero make:command Name` | Scaffold a console command |
| `php zero migrate` | Run pending migrations |
| `php zero migrate:rollback` | Roll back last batch |
| `php zero migrate:fresh` | Drop all + migrate |
| `php zero cache:clear` | Clear all cache |
| `php zero config:cache` | Compile config cache |
| `php zero route:list` | List all registered routes |
| `php zero queue:work` | Process jobs from the queue |
| `php zero schedule:run` | Run due scheduled tasks |

### Writing a command

```php
<?php

namespace App\Console\Commands;

use App\Core\Console\Command;

class SendDigestCommand extends Command
{
    protected string $signature    = 'digest:send';
    protected string $description  = 'Send the weekly digest email to all subscribers';

    public function handle(): void
    {
        $subscribers = Subscriber::where('active', true)->get();

        $bar = $this->output->progressBar(count($subscribers));

        foreach ($subscribers as $subscriber) {
            app(Mailer::class)->to($subscriber->email)->send(new DigestMailable($subscriber));
            $bar->advance();
        }

        $bar->finish();
        $this->info("Digest sent to {$subscribers->count()} subscribers.");
    }
}
```

Register the command in `config/console.php` or a service provider:

```php
// config/console.php
return [
    'commands' => [
        App\Console\Commands\SendDigestCommand::class,
    ],
];
```

### Command output helpers

```php
$this->info('Done.');
$this->error('Something went wrong.');
$this->warn('Deprecated usage detected.');
$this->line('Plain output.');

// Interactive
$name  = $this->ask('What is your name?');
$ok    = $this->confirm('Are you sure?');
$choice = $this->choice('Pick a driver', ['file', 'redis', 'database']);
$secret = $this->secret('Enter password:');
```

---

## 11. Cache

**Files:** `app/Core/Cache/CacheManager.php`, `app/Core/Cache/CacheRepository.php`, `app/Core/Cache/Drivers/`

### Drivers

| Driver | Class | Notes |
|---|---|---|
| `file` | `FileDriver` | Default; stores serialized values in `storage/cache/` |
| `array` | `ArrayDriver` | In-memory, lives for the current request only |
| `redis` | `RedisDriver` | Requires a Redis connection in `config/cache.php` |
| `database` | `DatabaseDriver` | Stores in `cache_entries` table |
| `null` | `NullDriver` | Discards all writes; useful in testing |

### CacheRepository

`CacheRepository` wraps any driver with a per-request in-memory layer. Frequently read keys (config, permissions) are served from memory on subsequent reads within the same request:

```php
$cache = app(CacheRepository::class);

// Get with default
$value = $cache->get('key', 'default');

// Put with TTL (seconds)
$cache->put('key', $value, 3600);

// Remember — fetch or compute and store
$user = $cache->remember("user:{$id}", 600, fn() => User::find($id));

// Delete
$cache->forget('key');

// Clear all
$cache->flush();
```

### Cache facade / helper

```php
cache('key');                        // get
cache(['key' => $value], 300);       // put for 300 seconds
cache()->remember('k', 60, $cb);     // remember
```

### Configuration

```php
// config/cache.php
return [
    'default' => env('CACHE_DRIVER', 'file'),

    'stores' => [
        'file'  => ['driver' => 'file', 'path' => storage_path('cache')],
        'redis' => ['driver' => 'redis', 'host' => env('REDIS_HOST', '127.0.0.1')],
    ],

    'prefix' => env('CACHE_PREFIX', 'zp_'),
];
```

---

## 12. Queue & Scheduler

### Queue

**Files:** `app/Core/Queue/QueueManager.php`, `app/Core/Queue/Worker.php`, `app/Core/Queue/Job.php`

#### Defining a job

```php
<?php

namespace App\Jobs;

use App\Core\Queue\Job;

class ProcessPaymentJob extends Job
{
    public function __construct(
        private readonly int $orderId
    ) {}

    public function handle(): void
    {
        $order = Order::findOrFail($this->orderId);
        app(PaymentService::class)->charge($order);
    }

    public function failed(\Throwable $e): void
    {
        logger()->error('Payment failed', ['order' => $this->orderId, 'error' => $e->getMessage()]);
    }
}
```

#### Dispatching jobs

```php
// Sync (runs immediately in current process)
ProcessPaymentJob::dispatch($orderId);

// To a named queue
ProcessPaymentJob::dispatch($orderId)->onQueue('payments');

// Delayed
ProcessPaymentJob::dispatch($orderId)->delay(30); // 30 seconds
```

#### Running a worker

```bash
php zero queue:work                   # default queue
php zero queue:work --queue=payments  # named queue
php zero queue:work --tries=3         # retry failed jobs 3 times
```

### Scheduler

**Files:** `app/Core/Scheduling/Schedule.php`, `app/Core/Scheduling/ScheduleManager.php`

Define scheduled tasks in a service provider's `schedules()` method or in `app/Console/Kernel.php`:

```php
public function schedules(Schedule $schedule): void
{
    // Artisan-style command strings
    $schedule->command('digest:send')->weekly()->sundays()->at('08:00');
    $schedule->command('cache:clear')->daily()->at('03:00');

    // Closures
    $schedule->call(function () {
        DB::table('sessions')->where('last_active', '<', now()->subHours(2))->delete();
    })->hourly();

    // Cron expression
    $schedule->command('report:generate')->cron('0 6 * * 1-5');
}
```

#### Running the scheduler

Add a single cron entry to the server:

```cron
* * * * * php /var/www/html/zero schedule:run >> /dev/null 2>&1
```

ZeroPing checks which tasks are due and executes them. Overlapping prevention uses a file-based mutex by default.

---

## 13. Testing

**Files:** `tests/TestCase.php`, `tests/Feature/`, `tests/Unit/`, `tests/bootstrap.php`

### Test structure

```
tests/
├── bootstrap.php           # Loads autoloader + sets test environment
├── TestCase.php            # Base test case (container setup, request helpers)
├── Feature/                # Integration / HTTP tests (boot the full app)
└── Unit/                   # Isolated unit tests (no framework boot required)
```

### Running tests

```bash
php zero test              # runs PHPUnit with phpunit.xml config
php zero test --filter=UserTest
php zero test --testsuite=Feature
vendor/bin/phpunit         # run directly
```

### TestCase base class

`Tests\TestCase` extends `PHPUnit\Framework\TestCase` and provides:

```php
$this->container               // fresh Container instance per test
$this->setRequestMethod('POST')
$this->setRequestUri('/api/users')
$this->setRequestHeaders(['Accept' => 'application/json'])
$this->setFormRequestBody(['name' => 'Alice'])
$this->setJsonRequestBody(['email' => 'alice@example.com'])
$this->withSessionData(['user_id' => 42])
$this->captureOutput(fn() => ...)  // capture echo'd output
```

`tearDown()` resets `$_GET`, `$_POST`, `$_SERVER`, and `$_SESSION` automatically.

### Unit test example

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Core\Container\Container;
use App\Services\UserService;
use App\Repositories\UserRepository;

class UserServiceTest extends TestCase
{
    public function test_creates_user_with_hashed_password(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())
            ->method('create')
            ->with($this->callback(fn($data) => password_verify('secret', $data['password'])));

        $this->container->instance(UserRepository::class, $repo);
        $service = $this->container->make(UserService::class);

        $service->create(['email' => 'test@example.com', 'password' => 'secret']);
    }
}
```

### Feature / HTTP test example

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class PostApiTest extends TestCase
{
    public function test_returns_post_list(): void
    {
        $this->setRequestMethod('GET');
        $this->setRequestUri('/api/v1/posts');
        $this->setRequestHeaders(['Accept' => 'application/json']);

        $output = $this->captureOutput(function () {
            // Boot the full request pipeline for this test
            $app = new \App\Core\Application\App(BASE_PATH);
            $app->handle();
        });

        $data = json_decode($output, true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
    }
}
```

### Database testing

Use a test database (set `DB_DATABASE=:memory:` for SQLite) and wrap each test in a transaction:

```php
class DatabaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }
}
```

### Mocking services

The container's `instance()` method is the cleanest way to swap implementations in tests:

```php
$this->container->instance(
    MailerInterface::class,
    new class implements MailerInterface {
        public array $sent = [];
        public function send(Mailable $m): void { $this->sent[] = $m; }
    }
);
```

---

## 14. Package Architecture

See `packages/ARCHITECTURE.md` for the complete package specification.

### Quick summary

A ZeroPing package is a Composer library with:

1. A `ServiceProvider` that extends `App\Providers\ServiceProvider`.
2. Contracts (interfaces) in `src/Contracts/`.
3. Default implementations bound in `register()`.
4. Side effects (routes, views, commands, migrations) wired in `boot()`.
5. A `composer.json` with `extra.zeroping.providers` for auto-discovery.

```
packages/zeroping/my-package/
├── composer.json
├── config/
│   └── my-package.php
├── src/
│   ├── Contracts/
│   │   └── MyServiceInterface.php
│   ├── MyService.php
│   └── MyPackageServiceProvider.php
└── tests/
```

**Registration** — add the provider to `config/app.php → providers`:

```php
'providers' => [
    App\Providers\AppServiceProvider::class,
    Zeroping\MyPackage\MyPackageServiceProvider::class,
],
```

---

## 15. Contributing to Core

### Adding a new subsystem

1. **Create the subsystem directory** under `app/Core/<SubsystemName>/`.
2. **Define contracts first** — add interfaces to `app/Core/Contracts/` or inside the subsystem directory. Depend on interfaces everywhere.
3. **Write the implementation** classes. Follow PSR-12 coding style.
4. **Wire via a service provider** in `app/Providers/<Subsystem>ServiceProvider.php`. Add it to `config/app.php`.
5. **Expose a helper** in `app/Helpers/helpers.php` if the subsystem warrants global access (e.g., `cache()`, `logger()`).
6. **Write tests** — unit tests under `tests/Unit/Core/<SubsystemName>/`, integration tests under `tests/Feature/`.
7. **Document** — update `framework-site/docs/` with a new `.md` file covering configuration, usage, and extension points.

### Code standards

- `declare(strict_types=1)` at the top of every PHP file.
- PHPDoc blocks on all public methods with `@param`, `@return`, `@throws`.
- No `echo` or `print` outside of views and the console layer.
- Exceptions must extend the appropriate framework exception base class.
- No static state except in `Container::$reflectionCache`, `Router::$routes`, and config-loaded singletons — document any new static state clearly.

### Pull request checklist

- [ ] `./vendor/bin/phpstan analyse` passes (level configured in `phpstan.neon`).
- [ ] `./vendor/bin/phpcs` passes (rules in `phpcs.xml.dist`).
- [ ] All new public APIs have tests.
- [ ] `CHANGELOG.md` has an entry under `[Unreleased]`.
- [ ] Documentation updated in `framework-site/docs/`.

### Running static analysis

```bash
vendor/bin/phpstan analyse --memory-limit=512M
vendor/bin/phpcs --standard=phpcs.xml.dist app/Core/
```

### Running the benchmark suite

```bash
php bench/run.php
```

Benchmarks live in `bench/` and cover hot paths: container resolution, route matching, and query building.
