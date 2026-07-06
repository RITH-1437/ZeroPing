# ZeroPing Framework

## Project Overview

ZeroPing is a lightweight, custom PHP framework designed for the Gaming Cafe Reservation System. It follows a layered architecture that separates framework infrastructure from application logic, providing a clean and maintainable codebase.

## Framework Goals

- Zero external dependencies (pure PHP)
- PSR-4 namespace conventions
- Clear separation between Core framework code and Application code
- Dependency injection via a service container
- Convention over configuration
- Modular architecture with service providers

## Folder Structure

```
.
├── app
│   ├── Contracts/                        # Application-level interfaces
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── CoffeeController.php
│   │   ├── HomeController.php
│   │   └── UserController.php
│   ├── Core/
│   │   ├── Application/
│   │   │   └── App.php
│   │   ├── Auth/
│   │   │   ├── AuthManager.php
│   │   │   ├── PasswordHasher.php
│   │   │   └── SessionGuard.php
│   │   ├── Config/
│   │   │   └── Env.php
│   │   ├── Console/
│   │   │   ├── Commands/
│   │   │   │   ├── LogTestCommand.php
│   │   │   │   ├── MakeControllerCommand.php
│   │   │   │   ├── MakeMigrationCommand.php
│   │   │   │   ├── MakeModelCommand.php
│   │   │   │   ├── MakeRepositoryCommand.php
│   │   │   │   ├── MakeServiceCommand.php
│   │   │   │   ├── MigrateCommand.php
│   │   │   │   └── RouteListCommand.php
│   │   │   ├── Stubs/
│   │   │   │   ├── controller.stub
│   │   │   │   ├── migration.stub
│   │   │   │   ├── model.stub
│   │   │   │   ├── repository.stub
│   │   │   │   └── service.stub
│   │   │   ├── Command.php
│   │   │   └── Console.php
│   │   ├── Container/
│   │   │   └── Container.php
│   │   ├── Database/
│   │   │   ├── Blueprint.php
│   │   │   ├── Database.php
│   │   │   ├── Grammar.php
│   │   │   ├── Migration.php
│   │   │   ├── MigrationRunner.php
│   │   │   ├── Model.php
│   │   │   ├── QueryBuilder.php
│   │   │   ├── Repository.php
│   │   │   └── Schema.php
│   │   ├── Events/
│   │   │   ├── Event.php
│   │   │   ├── EventDispatcher.php
│   │   │   └── Listener.php
│   │   ├── Logging/
│   │   │   ├── FileLogger.php
│   │   │   ├── Logger.php
│   │   │   ├── LogLevel.php
│   │   │   └── LogManager.php
│   │   ├── Routing/
│   │   │   └── Router.php
│   │   ├── Session/
│   │   │   └── Flash.php
│   │   ├── Validation/
│   │   │   └── Validator.php
│   │   └── View/
│   │       ├── Controller.php
│   │       └── View.php
│   ├── Events/
│   │   └── UserRegistered.php
│   ├── Exceptions/
│   ├── Helpers/
│   │   ├── Str.php
│   │   └── helpers.php
│   ├── Http/
│   │   ├── Middleware/
│   │   │   ├── AuthMiddleware.php
│   │   │   ├── GuestMiddleware.php
│   │   │   └── Middleware.php
│   │   ├── Request.php
│   │   └── Response.php
│   ├── Listeners/
│   │   └── LogUserRegistered.php
│   ├── Models/
│   │   ├── Coffee.php
│   │   ├── Tea.php
│   │   └── User.php
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── DatabaseServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   ├── LoggingServiceProvider.php
│   │   └── ServiceProvider.php
│   ├── Repositories/
│   │   ├── CoffeeRepository.php
│   │   └── UserRepository.php
│   ├── Services/
│   │   ├── AuthenticationService.php
│   │   └── CoffeeService.php
│   └── Support/
├── cli/
│   └── migrate.php
├── config/
│   ├── app.php
│   ├── config.php
│   ├── constants.php
│   ├── database.php
│   └── routes.php
├── database/
│   ├── backups/
│   ├── factories/
│   ├── migrations/
│   │   ├── 001_create_users_table.php
│   │   └── 2026_07_01_195638_create_coffees_table.php
│   └── seeders/
├── docs/
│   ├── project.md
│   └── SCOPE.md
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── fonts/
│   │   ├── icons/
│   │   ├── images/
│   │   ├── js/
│   │   └── uploads/
│   ├── index.php
│   └── test.php
├── storage/
│   ├── cache/
│   ├── logs/
│   ├── sessions/
│   └── temp/
├── views/
│   ├── admin/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── cafes/
│   ├── components/
│   ├── customer/
│   ├── errors/
│   │   └── 404.php
│   ├── home/
│   │   ├── about.php
│   │   └── index.php
│   ├── layouts/
│   │   ├── app.php
│   │   └── guest.php
│   ├── owner/
│   ├── partials/
│   └── reservation/
├── .env
├── .gitignore
├── README.md
├── composer.json
├── composer.lock
└── zero
```

## Architecture Layers

The framework uses a two-tier architecture:

### Core Layer (`app/Core/`)

Framework infrastructure that remains unchanged across projects:

| Directory | Purpose |
|-----------|---------|
| `Application/` | Bootstrap and application lifecycle (`App`) |
| `Auth/` | Session management, authentication, password hashing |
| `Config/` | Environment variable loading (`Env`) |
| `Console/` | CLI command system and stubs |
| `Container/` | Dependency injection container |
| `Database/` | PDO connection, QueryBuilder, Schema, Model, Migration, Repository |
| `Events/` | Event dispatcher and listener contracts |
| `Logging/` | PSR-3-inspired logging system |
| `Routing/` | URL routing and dispatching |
| `Session/` | Flash messages and session utilities |
| `Validation/` | Input validation |
| `View/` | View rendering engine |

### Application Layer (`app/`)

Project-specific logic that uses the Core:

| Directory | Purpose |
|-----------|---------|
| `Controllers/` | HTTP request handlers |
| `Events/` | Domain events (e.g., `UserRegistered`) |
| `Exceptions/` | Application exceptions |
| `Helpers/` | Utility functions |
| `Http/` | Request, Response, Middleware |
| `Listeners/` | Event listeners (e.g., `LogUserRegistered`) |
| `Models/` | Eloquent-style models |
| `Providers/` | Service providers |
| `Repositories/` | Data access objects |
| `Services/` | Business logic |

## Bootstrap Flow

### HTTP Request (`public/index.php`)

```
index.php
  → vendor/autoload.php (Composer PSR-4)
  → config/config.php
      → Env::load(.env)
      → constants.php
      → database.php (DB_* defines)
      → app.php
  → App::run()
      → App::boot()
          → new Container()
          → App::register() — loops through providers
              → AppServiceProvider::register() + boot()
              → DatabaseServiceProvider::register() + boot()
              → EventServiceProvider::register() + boot()
              → LoggingServiceProvider::register() + boot()
      → Router::dispatch()
          → loads config/routes.php
          → matches URI to route
          → executes middleware stack
          → resolves controller via Container
          → calls controller method
```

### CLI Command (`zero`)

```
zero <command>
  → vendor/autoload.php
  → config/constants.php
  → config/config.php
  → App::boot()
  → Console::run($argv)
      → matches command to Command class
      → calls handle()
```

## Dependency Injection

The `App\Core\Container\Container` provides:

- **`bind($abstract, $concrete)`** — Register a non-shared binding
- **`singleton($abstract, $concrete)`** — Register a shared instance
- **`instance($abstract, $object)`** — Register an existing object
- **`make($abstract)`** — Resolve a class (auto-wires constructor dependencies via reflection)

The container is created once in `App::boot()` and shared across the entire request lifecycle.

## Service Providers

Providers register and boot framework services. Each provider extends `App\Providers\ServiceProvider`:

```php
abstract class ServiceProvider
{
    protected Container $container;

    abstract public function register(): void;

    public function boot(): void { /* optional */ }
}
```

### Registered Providers

| Provider | Registers | Boots |
|----------|-----------|-------|
| `AppServiceProvider` | `Container` singleton | — |
| `DatabaseServiceProvider` | `Database` singleton (PDO) | — |
| `EventServiceProvider` | `EventDispatcher` singleton | Binds `UserRegistered` → `LogUserRegistered` |
| `LoggingServiceProvider` | `Logger` singleton (`FileLogger`) | Logs framework boot message |

## Routing

Routes are defined in `config/routes.php` using `App\Core\Routing\Router`:

```php
// Basic routes
Router::get('/path', [Controller::class, 'method']);
Router::post('/path', [Controller::class, 'method']);

// Route groups with prefix
Router::prefix('/admin', function () {
    Router::get('/dashboard', [...]);
});

// Route groups with middleware
Router::middleware(['auth'], function () {
    Router::get('/dashboard', [...]);
});

// Dynamic parameters
Router::get('/users/{id}', [UserController::class, 'show']);
```

The router supports:
- GET and POST methods
- Dynamic route parameters (`{id}`)
- Route prefixing
- Middleware groups
- 404 fallback

## HTTP Layer

### Request (`App\Http\Request`)

Static utility for accessing request data:

```php
Request::method();       // GET, POST, etc.
Request::isGet();        // bool
Request::isPost();       // bool
Request::input('key');   // $_POST['key'] or $_GET['key']
Request::all();          // array_merge($_GET, $_POST)
Request::has('key');     // bool
```

### Response (`App\Http\Response`)

Static utility for sending responses:

```php
Response::redirect('/login');   // header Location + exit
Response::json($data);          // JSON + exit
```

### Middleware

Middleware extends `App\Http\Middleware\Middleware` and implements `handle()`:

- **`AuthMiddleware`** — Redirects to `/login` if user is not authenticated
- **`GuestMiddleware`** — Redirects to `/` if user is already authenticated

Middleware is resolved by name convention: `Router::middleware(['auth'], ...)` resolves to `App\Http\Middleware\AuthMiddleware`.

## Authentication

The auth system consists of three components:

### SessionGuard (`App\Core\Auth\SessionGuard`)

Low-level session wrapper:

```php
SessionGuard::start();          // session_start() if needed
SessionGuard::set('key', $val); // $_SESSION['key'] = $val
SessionGuard::get('key');       // $_SESSION['key'] ?? $default
SessionGuard::has('key');       // isset check
SessionGuard::remove('key');    // unset
SessionGuard::destroy();        // clear + session_destroy()
```

### AuthManager (`App\Core\Auth\AuthManager`)

High-level auth facade:

```php
AuthManager::login($user);  // stores user array in session
AuthManager::logout();      // destroys session
AuthManager::user();        // returns current user array
AuthManager::check();       // bool — is authenticated?
AuthManager::id();          // int|null — current user ID
```

### PasswordHasher (`App\Core\Auth\PasswordHasher`)

BCrypt password hashing:

```php
PasswordHasher::make($password);          // hash
PasswordHasher::check($password, $hash);  // verify
```

## Database Layer

### Connection (`App\Core\Database\Database`)

Singleton PDO wrapper using constants from `config/database.php`:

```php
$pdo = Database::connect(); // PDO instance
```

### Model (`App\Core\Database\Model`)

Abstract base model providing CRUD via QueryBuilder:

```php
abstract class Model
{
    protected PDO $db;      // auto-connected
    protected string $table;

    public function query(): QueryBuilder;
    public function all(): array;
    public function find(int $id): ?array;
    public function findBy(string $column, mixed $value): ?array;
    public function create(array $data): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
```

## Query Builder

`App\Core\Database\QueryBuilder` provides a fluent interface for SQL queries:

```php
$results = $model->query()
    ->where('status', 'active')
    ->orWhere('role', 'admin')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->offset(0)
    ->get();

$first = $model->query()->where('id', 1)->first();
$count = $model->query()->count();
$exists = $model->query()->where('email', $email)->exists();
```

## Schema Builder

### Blueprint (`App\Core\Database\Blueprint`)

Fluent column definition:

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->integer('age');
    $table->boolean('active');
    $table->text('bio');
    $table->timestamps();
});
```

### Schema (`App\Core\Database\Schema`)

```php
Schema::create('table', $callback);
Schema::drop('table');
```

## Migration System

### Migration Base (`App\Core\Database\Migration`)

```php
abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;
}
```

### MigrationRunner (`App\Core\Database\MigrationRunner`)

- Creates a `migrations` tracking table
- Scans `database/migrations/*.php`
- Tracks batch numbers
- Skips already-executed migrations

### Available Commands

```bash
php zero migrate           # Run pending migrations
php zero make:migration    # Create a new migration file
```

## Event System

### Core Contracts

- **`App\Core\Events\Event`** — Abstract base class for all events
- **`App\Core\Events\Listener`** — Interface with `handle(Event $event): void`
- **`App\Core\Events\EventDispatcher`** — Registers listeners and dispatches events

### Application Events

| Event | Listeners |
|-------|-----------|
| `App\Events\UserRegistered` | `App\Listeners\LogUserRegistered` |

### Usage

```php
$dispatcher = App::container()->make(EventDispatcher::class);
$dispatcher->dispatch(new UserRegistered($user));
```

## Logging System

PSR-3-inspired logging with a file-based implementation:

### Logger Interface (`App\Core\Logging\Logger`)

```php
interface Logger
{
    public function emergency(string $message): void;
    public function alert(string $message): void;
    public function critical(string $message): void;
    public function error(string $message): void;
    public function warning(string $message): void;
    public function notice(string $message): void;
    public function info(string $message): void;
    public function debug(string $message): void;
    public function log(string $level, string $message): void;
}
```

### Log Levels (`App\Core\Logging\LogLevel`)

EMERGENCY, ALERT, CRITICAL, ERROR, WARNING, NOTICE, INFO, DEBUG

### FileLogger (`App\Core\Logging\FileLogger`)

Writes to `storage/logs/app.log` with timestamps.

### Usage

```php
$logger = App::container()->make(Logger::class);
$logger->info('User logged in');
$logger->error('Database connection failed');
```

## Console System

The CLI entry point is the `zero` file, which boots the framework and runs `App\Core\Console\Console`.

### Console Router (`App\Core\Console\Console`)

Routes `$argv[1]` to the appropriate command class.

### Command Base (`App\Core\Console\Command`)

Abstract base for all commands.

## Current CLI Commands

| Command | Class | Description |
|---------|-------|-------------|
| `php zero migrate` | `MigrateCommand` | Run pending database migrations |
| `php zero make:model` | `MakeModelCommand` | Generate a new model file |
| `php zero make:controller` | `MakeControllerCommand` | Generate a new controller file |
| `php zero make:service` | `MakeServiceCommand` | Generate a new service file |
| `php zero make:repository` | `MakeRepositoryCommand` | Generate a new repository file |
| `php zero make:migration` | `MakeMigrationCommand` | Generate a new migration file |
| `php zero route:list` | `RouteListCommand` | Display all registered routes |
| `php zero log:test` | `LogTestCommand` | Test the logging system |

## Framework Lifecycle

1. **Autoload** — Composer PSR-4 maps `App\` → `app/`, `Config\` → `config/`
2. **Config** — `.env` loaded, constants defined, database config set
3. **Boot** — `App::boot()` creates Container, registers all providers
4. **Provider Register** — Each provider binds services to the container
5. **Provider Boot** — Each provider performs post-registration setup (e.g., event wiring)
6. **Dispatch** — Router matches the request URI, executes middleware, resolves controller
7. **Response** — Controller method runs, returns HTML/JSON/redirect

## Current Features

- Authentication (login/register/logout) with session-based guards
- Role-ready user model (customer, admin, etc.)
- CRUD operations via Model + QueryBuilder
- Database migrations with batch tracking
- Schema builder with Blueprint
- Base Repository pattern for data access
- Service layer for business logic
- Event system with dispatcher and listeners
- File-based logging with PSR-3-style levels
- Middleware stack (auth, guest)
- Flash messages for session-based feedback
- View rendering with layouts and partials
- Console commands for code generation and database management
- Dependency injection container with auto-wiring

## Completed Phases

1. **Phase 1** — Core framework (Container, Routing, View, Request/Response)
2. **Phase 2** — Database layer (PDO, Model, QueryBuilder, Schema, Migrations)
3. **Phase 3** — Authentication (SessionGuard, AuthManager, PasswordHasher, Middleware)
4. **Phase 4** — Service layer and Repository pattern
5. **Phase 5** — Event system (EventDispatcher, Listener contract)
6. **Phase 6** — Logging system (Logger interface, FileLogger, LogLevel)
7. **Phase 7** — Console commands (make:model, make:controller, etc.)
8. **Phase 8** — Architecture refactoring (PSR-4 alignment, Core/Application separation)

## Remaining Roadmap

- Query Builder: joins, aggregates, having, groupBy
- Migration: down/rollback support
- Validation: integrate Validator into request flow
- API layer: JSON response helpers, API routing
- Template engine: replace raw PHP views
- CSRF protection middleware
- Rate limiting middleware
- File upload handling
- Queue/job system
- Testing infrastructure (PHPUnit)
