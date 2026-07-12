# ZeroPing Package Architecture

A contract-first, provider-driven packaging model for the ZeroPing Framework, inspired by
Laravel Packages but grounded in ZeroPing's **actual** extension surface:

- `App\Providers\ServiceProvider` with `register()` / `boot()` and a `Container`.
- `Container::bind()` / `singleton()` / `instance()` / `make()`.
- Config loaded from `config/*.php` into a `ConfigRepository`; providers listed in `config('app.providers')`.
- Helpers autoloaded from `app/Helpers/helpers.php` (no `asset()` / `url()` / `old()` globals).

> Scope: this document **designs** the architecture (interfaces, folder layout, conventions,
> extension points, dependency registration). Packages are **not** fully implemented. The
> `zeroping/support` base package and the `zeroping/queue` reference package are scaffolded as
> working templates; every other package follows the same skeleton with its own contracts.

---

## 1. Goals & Principles

| Principle | Meaning |
| --- | --- |
| **Contract-first** | Every package ships `src/Contracts/*` interfaces. Apps depend on contracts, not concretes. |
| **Provider-driven** | All wiring happens in a `register()`/`boot()` service provider. No global side effects. |
| **Explicit registration** | A package is inert until the host adds its provider to `config/app.providers` and binds contracts. |
| **PSR-4 + Composer** | Each package is a Composer package under the `Zeroping\<Package>` namespace. |
| **No framework coupling** | Packages depend on `App\Core\*` contracts and on `zeroping/support`, never on app code. |
| **Override by config** | Default implementations are bound in `register()` but can be replaced by the host app. |

---

## 2. How ZeroPing Boots Today (the extension model)

```php
// app/Core/Application/App.php (simplified)
$container = new Container();
Env::load(...);                 // populates $_ENV
loadConfig();                   // config/*.php -> ConfigRepository
registerProviders();            // for each class in config('app.providers'):
                                //   $p = new $class($container); $p->register();
                                //   then: $p->boot();
```

Two phases matter for packages:

- **`register()`** — bind services into the container. No side effects, no other providers assumed ready.
- **`boot()`** — wire routes, views, migrations, commands, event listeners. All `register()`s have run.

### Gaps this architecture fills

The current core is app-centric. To support packages we introduce (in `zeroping/support`)
thin **registries** that providers push into, plus small core adapters:

| Need | Current core | Package extension point |
| --- | --- | --- |
| Console commands | Hard-coded `switch` in `Console` | `CommandRegistry` (singleton) consulted by `Console` |
| Views | Single `View::setBasePath()` | `View::addNamespace($ns, $path)` |
| Migrations | One `database/migrations` path | `MigrationLoader::addPath($path)` |
| Routes | `config/routes.php` only | `Router::*` calls from a provider's `boot()` |
| Assets | Manual copy | `publishes()` manifest |

These additions are **additive** and documented in §6.

---

## 3. Canonical Package Folder Structure

```
packages/
└── zeroping/
    ├── support/                 # foundational base package (every package extends it)
    │   ├── composer.json
    │   ├── config/
    │   │   └── support.php
    │   └── src/
    │       ├── ServiceProvider.php        # base provider with load*/publishes/commands
    │       ├── Console/
    │       │   ├── Command.php            # extends App\Core\Console\Command, adds name()/argument()
    │       │   └── CommandRegistry.php
    │       └── Foundation/
    │           ├── MigrationLoader.php    # aggregates migration paths
    │           └── ViewFinder.php         # registers view namespaces
    └── queue/                    # reference package (full skeleton)
        ├── composer.json
        ├── config/
        │   └── queue.php
        ├── database/
        │   └── migrations/               # package migrations
        ├── resources/
        │   └── views/                   # package views (namespaced)
        ├── src/
        │   ├── Contracts/
        │   │   ├── Queue.php
        │   │   ├── Job.php
        │   │   └── QueueManager.php
        │   ├── QueueManager.php          # default implementation (stub)
        │   ├── Console/
        │   │   └── WorkCommand.php
        │   └── QueueServiceProvider.php
        └── tests/
```

Every package mirrors `queue/` (swap the name). The only required files are
`composer.json`, `src/<Name>ServiceProvider.php`, and `src/Contracts/*`.

---

## 4. Package Conventions

### 4.1 Namespacing & Composer

```json
{
    "name": "zeroping/queue",
    "type": "library",
    "description": "Queueing for the ZeroPing Framework",
    "license": "MIT",
    "require": {
        "php": ">=8.1",
        "zeroping/support": "^1.0"
    },
    "autoload": {
        "psr-4": {
            "Zeroping\\Queue\\": "src/"
        }
    },
    "extra": {
        "zeroping": {
            "providers": ["Zeroping\\Queue\\QueueServiceProvider"]
        }
    }
}
```

- **Vendor namespace:** `Zeroping\<Package>\` mapped to `src/`.
- **Provider:** `Zeroping\<Package>\<Package>ServiceProvider` (e.g. `QueueServiceProvider`).
- **Contracts:** `Zeroping\<Package>\Contracts\*` — **interfaces only**.
- **Config file:** `config/<package>.php` (keyed by package short name, e.g. `queue`).
- **`extra.zeroping.providers`** is an optional discovery hint (see §7.3).

### 4.2 Versioning

Semantic versioning. `^1.0` for stable packages. Mark experimental packages `^0.x`
and document breaking changes in the package `CHANGELOG.md`.

---

## 5. The Base Service Provider (`zeroping/support`)

All packages extend `Zeroping\Support\ServiceProvider` instead of the framework's bare
`App\Providers\ServiceProvider`. It adds the standard loaders and a `publishes()` manifest.

```php
namespace Zeroping\Support;

use App\Core\Container\Container;
use App\Providers\ServiceProvider as Base;

abstract class ServiceProvider extends Base
{
    protected array $pathsToPublish = [];
    protected array $migrationPaths = [];

    public function __construct(Container $container)
    {
        parent::__construct($container);
    }

    // Merge a package config file into the host's config repository
    // under the same key, without overwriting host values.
    protected function mergeConfigFrom(string $path, string $key): void
    {
        $defaults = require $path;
        $existing  = \App\Core\Config\Config::get($key, []);
        \App\Core\Config\Config::set(
            $key,
            array_replace_recursive($defaults, $existing)
        );
    }

    // Require a routes file that calls Router::get/post/prefix/middleware.
    protected function loadRoutesFrom(string $path): void
    {
        if (file_exists($path)) {
            require $path;
        }
    }

    // Register a namespaced view path: view('queue::email') -> resources/views/email.php
    protected function loadViewsFrom(string $path, string $namespace): void
    {
        \App\Core\View\View::addNamespace($namespace, $path);
    }

    // Register a migration directory with the aggregated loader.
    protected function loadMigrationsFrom(string $path): void
    {
        \Zeroping\Support\Foundation\MigrationLoader::addPath($path);
    }

    // Register console commands (classes extending Zeroping\Support\Console\Command).
    protected function commands(array $classes): void
    {
        foreach ($classes as $class) {
            \Zeroping\Support\Console\CommandRegistry::register($class);
        }
    }

    // Declare publishable assets (config, migrations, views) for `php zero vendor:publish`.
    protected function publishes(array $paths, string $group = 'default'): void
    {
        foreach ($paths as $from => $to) {
            $this->pathsToPublish[$group][$from] = $to;
        }
    }

    public function boot(): void {}
}
```

> `Config::set()` and `View::addNamespace()` are **proposed core additions** (tiny, additive).
> Until merged, a package may call them via the `zeroping/support` shim which polyfills them.

---

## 6. Extension Points (the contract with core)

| # | Extension point | How a package uses it |
| --- | --- | --- |
| 1 | **Service Provider** | `register()` binds contracts; `boot()` wires the rest. |
| 2 | **Container bindings** | `$this->container->singleton(Contract::class, Impl::class)`. |
| 3 | **Config** | Ship `config/<pkg>.php`; `mergeConfigFrom()` in `register()`. |
| 4 | **Routes** | `loadRoutesFrom()` → `Router::get/post/prefix/middleware` in `boot()`. |
| 5 | **Views** | `loadViewsFrom($dir, 'pkg')` → render with `view('pkg::file')`. |
| 6 | **Migrations** | `loadMigrationsFrom($dir)` → picked up by `migrate`. |
| 7 | **Console commands** | `commands([XCommand::class])` → `CommandRegistry`. |
| 8 | **Events / Listeners** | Bind a `Dispatcher` singleton; `Dispatcher::listen()` in `boot()`. |
| 9 | **Middleware** | Register short-name middleware and attach via `Router::middleware()` in `boot()`. |
| 10 | **Publishable assets** | `publishes()` → consumed by a `vendor:publish` command. |

### 6.1 Proposed core adapters (additive)

```php
// App\Core\View\View — add:
public static function addNamespace(string $namespace, string $path): void
{
    self::$namespaces[$namespace] = rtrim($path, '/');
}
// findView() then also checks: $namespaces[$ns].'/'.$view.'.php'

// App\Core\Console\Console — before the switch:
if ($cmd = CommandRegistry::find($argv[1] ?? '')) {
    (new $cmd())->handle();
    return;
}
```

Nothing existing is removed; packages simply gain surface area.

---

## 7. Dependency Registration

### 7.1 Install the package

```bash
composer require zeroping/queue
```

For local development of a package, use a path repository in the host `composer.json`:

```json
{
    "repositories": [
        { "type": "path", "url": "packages/zeroping/queue" }
    ]
}
```

### 7.2 Register the provider

Add the provider class to `config/app.php` → `providers`:

```php
return [
    'providers' => [
        App\Providers\AppServiceProvider::class,
        Zeroping\Queue\QueueServiceProvider::class,   // <-- package provider
    ],
];
```

On boot, the framework calls `register()` then `boot()` on it.

### 7.3 Optional auto-discovery

If the host enables discovery, Composer's `extra.zeroping.providers` is read after `composer dump-autoload` and appended automatically. Keep auto-discovery opt-in to avoid surprise side effects.

### 7.4 Bind & override

Inside the package `register()`:

```php
$this->container->singleton(
    \Zeroping\Queue\Contracts\QueueManager::class,
    \Zeroping\Queue\QueueManager::class
);
```

The host app can later rebind the contract to its own driver:

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->container->singleton(
        \Zeroping\Queue\Contracts\QueueManager::class,
        \App\Services\RedisQueueManager::class
    );
}
```

Because `register()` runs for every provider before any `boot()`, the host's binding wins
(later `singleton()` overwrites the earlier one).

---

## 8. Per-Package Architecture

Each package below lists: purpose, namespace, **key contracts** (the designed interfaces),
the default binding, and the extension points it exposes. Concrete logic is intentionally
omitted — these are the architecture contracts.

### 8.1 ZeroPing Auth (`zeroping/auth`)

Purpose: pluggable authentication guards and user providers.

```php
namespace Zeroping\Auth\Contracts;

interface Authenticatable {                       // the user object contract
    public function getAuthIdentifier(): mixed;
    public function getAuthPassword(): ?string;
}

interface UserProvider {
    public function retrieveById(mixed $id): ?Authenticatable;
    public function retrieveByCredentials(array $creds): ?Authenticatable;
    public function validateCredentials(Authenticatable $u, array $creds): bool;
}

interface Guard {
    public function check(): bool;
    public function user(): ?Authenticatable;
    public function login(Authenticatable $user): void;
    public function logout(): void;
}
```

- Default binding: `Guard` → `SessionGuard`, `UserProvider` → `DatabaseUserProvider`.
- Extension: new guards (token, jwt) bound by the host; middleware `auth:<guard>`.
- Note: works **on top of** the existing `App\Core\Auth\AuthManager` (which stays the default session guard).

### 8.2 ZeroPing Queue (`zeroping/queue`) — reference package

```php
namespace Zeroping\Queue\Contracts;

interface Job {
    public function handle(): void;          // process the job
    public function failed(\Throwable $e): void;
}

interface Queue {
    public function push(Job $job, ?string $queue = null): void;
    public function pop(?string $queue = null): ?Job;
    public function later(Job $job, int $seconds): void;
}

interface QueueManager {
    public function connection(?string $name = null): Queue;
    public function addConnection(string $name, array $config): void;
}
```

- Default binding: `QueueManager` → `Zeroping\Queue\QueueManager` (reads `config/queue.php`).
- Extension: new drivers (`redis`, `sqs`, `database`) by binding a `Queue` implementation and
  registering the connection in `boot()`.
- Commands: `queue:work`, `queue:listen`, `queue:retry`, `queue:failed` (via `commands()`).

### 8.3 ZeroPing Mail (`zeroping/mail`)

```php
namespace Zeroping\Mail\Contracts;

interface Mailable {
    public function build(): Message;        // returns a configured Message
}

interface Mailer {
    public function send(Mailable $mailable): void;
    public function to(string|array $address): static;
    public function queue(Mailable $mailable): void;   // integrates with zeroping/queue
}

interface Transport {
    public function send(Message $message): void;     // smtp, sendmail, log, array
}
```

- Default binding: `Mailer` → `Mailer` (transport from `config/mail.php`).
- Extension: custom transports (SES, Mailgun) as `Transport` implementations.

### 8.4 ZeroPing Cache (`zeroping/cache`)

```php
namespace Zeroping\Cache\Contracts;

interface Store {
    public function get(string $key): mixed;
    public function put(string $key, mixed $value, int $ttl): void;
    public function forget(string $key): bool;
    public function flush(): void;
}

interface Repository {
    public function remember(string $key, int $ttl, callable $cb): mixed;
    public function many(array $keys): array;
}
```

- Default binding: `Repository` → `Repository` backed by a `Store` (file/array/redis).
- Extension: new stores (`redis`, `memcached`, `database`) as `Store` implementations.
- Note: augments the existing `CacheManager`; the package's `Repository` becomes the app-wide `cache()` return when bound.

### 8.5 ZeroPing Notifications (`zeroping/notifications`)

```php
namespace Zeroping\Notifications\Contracts;

interface Notification {
    /** @return array<string, mixed> channel => data */
    public function via(): array;
    public function toMail(): \Zeroping\Mail\Contracts\Mailable;
    public function toArray(): array;          // database channel
}

interface Channel {
    public function send($notifiable, Notification $n): void;  // mail, database, sms, broadcast
}

interface Notifiable {
    public function routeNotificationFor(string $channel): mixed;
}
```

- Default binding: a `NotificationSender` singleton dispatches via registered `Channel`s.
- Extension: new channels (`slack`, `push`) by binding a `Channel`.

### 8.6 ZeroPing Events (`zeroping/events`)

```php
namespace Zeroping\Events\Contracts;

interface Dispatcher {
    public function listen(string $event, callable|string $handler): void;
    public function dispatch(object $event): void;
    public function push(string $event, array $payload): void; // queue-aware
}

interface Listener {
    public function handle(object $event): void;
}
```

- Default binding: `Dispatcher` → `Dispatcher` (sync; can delegate to `zeroping/queue`).
- Extension: queued listeners (implement `ShouldQueue` marker + `queue()`), wildcard `*` events.

### 8.7 ZeroPing Scheduler (`zeroping/scheduler`)

```php
namespace Zeroping\Scheduler\Contracts;

interface Scheduler {
    public function call(callable $task, string $expression): static;
    public function command(string $command, string $expression): static;
    public function runDue(): void;            // invoked by `schedule:run`
}

interface Mutex {
    public function create(string $name): bool;   // prevent overlapping
    public function release(string $name): void;
}
```

- Default binding: `Scheduler` → `Scheduler` (cron expressions), `Mutex` → `FileMutex`.
- Commands: `schedule:run`, `schedule:list`, `schedule:test`.
- Extension: new mutex backends (`redis`, `database`).

### 8.8 ZeroPing API (`zeroping/api`)

```php
namespace Zeroping\Api\Contracts;

interface Resource {
    public function toArray(): array;          // JSON:API / HAL style output
    public function with(): array;            // eager includes
}

interface Transformer {
    public function transform($model): array;
}

interface ApiGuard {
    public function authenticate(): ?Authenticatable;   // token / jwt / passport
}
```

- Provides a thin JSON response layer and resource transformers; integrates with `zeroping/auth`.
- Extension: media types, pagination strategies, `ApiGuard` implementations.

### 8.9 ZeroPing Debugbar (`zeroping/debugbar`)

```php
namespace Zeroping\Debugbar\Contracts;

interface Panel {
    public function name(): string;
    public function collect(): array;          // data shown in the panel
}

interface Debugbar {
    public function addPanel(Panel $panel): void;
    public function render(): string;          // injects HTML before </body>
}
```

- Default binding: `Debugbar` → `Debugbar` (only active when `APP_DEBUG=true`).
- Extension: custom panels (queries, routes, auth, cache) via `addPanel()`.

### 8.10 ZeroPing Scout (`zeroping/scout`)

```php
namespace Zeroping\Scout\Contracts;

interface Searchable {
    public function toSearchArray(): array;
    public function searchableAs(): string;    // index name
}

interface Engine {
    public function update(Searchable $m): void;
    public function delete(Searchable $m): void;
    public function search(string $index, string $query, int $perPage): array;
}
```

- Default binding: `Engine` → `DatabaseEngine` (LIKE/Fulltext); `algolia`/`meilisearch` as alt.
- Extension: new search engines by binding an `Engine`.

### 8.11 ZeroPing Storage (`zeroping/storage`)

```php
namespace Zeroping\Storage\Contracts;

interface Filesystem {
    public function put(string $path, string $contents): void;
    public function get(string $path): string;
    public function url(string $path): string;
    public function delete(string $path): bool;
}

interface FilesystemManager {
    public function disk(?string $name = null): Filesystem;
    public function extend(string $name, callable $factory): void;
}
```

- Default binding: `FilesystemManager` → manager reading `config/filesystems.php`.
- Extension: new disks (s3, gcs, ftp) via `extend()`.
- Note: augments the existing `App\Core\Filesystem\FilesystemManager` (same `disk()` API).

### 8.12 ZeroPing Passport (`zeroping/passport`)

Purpose: OAuth2 server on top of `zeroping/auth` + `zeroping/api`.

```php
namespace Zeroping\Passport\Contracts;

interface TokenRepository {
    public function createPersonalToken(int $userId, string $name, array $scopes): Token;
    public function find(string $id): ?Token;
    public function revoke(string $id): void;
}

interface ClientRepository {
    public function createPasswordGrantClient(int $userId, string $name, string $redirect): Client;
}

interface AuthorizesRequests {                 // issues/validates bearer tokens
    public function issueToken(array $credentials): ?Token;
    public function validateToken(string $bearer): ?Authenticatable;
}
```

- Default binding: repositories + `AuthorizesRequests` (e.g. `BearerGuard`).
- Commands: `passport:install`, `passport:client`, `passport:keys`.
- Extension: grant types (password, client credentials, authorization code).

---

## 9. Package Development Guidelines (third-party)

A third-party developer building `acme/audit-log` for ZeroPing follows these steps.

### 9.1 Scaffold

```bash
mkdir -p packages/acme/audit-log/src/Contracts
cd packages/acme/audit-log
composer init --name=acme/audit-log --type=library
```

`composer.json` autoload:

```json
{ "autoload": { "psr-4": { "Acme\\AuditLog\\": "src/" } } }
```

### 9.2 Define contracts first

```php
// src/Contracts/AuditSink.php
namespace Acme\AuditLog\Contracts;

interface AuditSink {
    public function write(string $message, array $context): void;
}
```

### 9.3 Write the service provider

```php
namespace Acme\AuditLog;

use Acme\AuditLog\Contracts\AuditSink;
use Acme\AuditLog\DatabaseSink;
use Zeroping\Support\ServiceProvider;

class AuditLogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/audit-log.php', 'audit-log');

        $this->container->singleton(AuditSink::class, function ($c) {
            $driver = config('audit-log.driver', 'database');
            return match ($driver) {
                'database' => new DatabaseSink(),
                default    => new DatabaseSink(),
            };
        });
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->commands([FlushAuditCommand::class]);
    }
}
```

### 9.4 Ship config & migrations

`config/audit-log.php` returns an array. `database/migrations/*_create_audit_logs.php`
uses the framework's `Schema`/`Migration` (same as app migrations).

### 9.5 Register in the host app

```jsonc
// composer.json (host)
{ "repositories": [ { "type": "path", "url": "packages/acme/audit-log" } ] }
```
```bash
composer require acme/audit-log
```
```php
// config/app.php
'providers' => [ /* ... */ Acme\AuditLog\AuditLogServiceProvider::class ],
```

### 9.6 Best practices

- **Depend on contracts, not concretes.** Type-hint `AuditSink`, never `DatabaseSink`.
- **Never call `new` on framework internals you don't own.** Resolve via the container.
- **Keep `register()` side-effect free.** Only bind. Do route/view/migration work in `boot()`.
- **Don't assume boot order.** If you need another package, document it as a Composer `require`.
- **Make it overridable.** Always bind a default but let the host rebind the contract.
- **Prefix publishable assets** so `vendor:publish` groups don't collide (`--tag=audit-log-config`).
- **Test against the framework.** Use `Tests\TestCase`; boot your provider in `setUp()`.

### 9.7 Publishing to Packagist

1. Push to a public VCS.
2. Submit the VCS URL at packagist.org.
3. Tag releases (`git tag 1.0.0 && git push --tags`); Packagist picks up semver tags.
4. Document the provider class, config keys, and any `publishes()` groups.

---

## 10. Reference Implementation

The following are scaffolded in this repository and serve as copy-paste templates:

- `packages/zeroping/support/` — base `ServiceProvider`, `CommandRegistry`, `MigrationLoader`, `ViewFinder`.
- `packages/zeroping/queue/` — full reference: `Contracts/Queue|Job|QueueManager`, default `QueueManager`, `QueueServiceProvider`, `config/queue.php`, console commands.

All other packages in §8 follow the **same skeleton** with their own contracts. To create a new
package, copy `packages/zeroping/queue/`, rename the namespace, and replace the contracts.
