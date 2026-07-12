# Service Container & Dependency Injection

ZeroPing ships a lightweight inversion-of-control container at `App\Core\Container\Container`. You use it to register bindings and resolve (auto-wire) classes — including into controller constructors. Access it through the global `app()` helper.

## Binding services

```php
use App\Core\Cache\CacheManager;

// Bind a non-shared instance (new resolution each time)
app()->bind(CacheManager::class, fn () => new CacheManager());

// Bind a shared (singleton) instance
app()->singleton(CacheManager::class, fn () => new CacheManager());

// Register an already-created object
app()->instance('app.boot', new \App\Core\Container\Container());
```

## Resolving services

```php
// No argument -> the Container instance
$container = app();

// Resolve (auto-wired) a class
$cache = app(CacheManager::class);
$cache = app()->make(CacheManager::class);
```

## Automatic dependency injection

`make()` uses reflection to build a class. Any type-hinted, non-primitive constructor parameter is recursively resolved from the container. Because the router resolves controllers through the container, you can inject dependencies straight into a controller constructor:

```php
namespace App\Controllers;

use App\Core\View\Controller;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $reports) {}

    public function index(): void
    {
        $this->view('reports/index', ['rows' => $this->reports->latest()], 'site');
    }
}
```

## Service providers

Providers in `app/Providers` receive the container and are the recommended place to register bindings. For example, `App\Providers\ScheduleServiceProvider` registers the `ScheduleManager` as a singleton.

## Best Practices

Register shared services (database, cache, mail) as `singleton()` so the same instance is reused. Inject dependencies via constructors rather than calling `app()` throughout your code.

## Common Mistakes

Looking for `resolve()`, `get()`, `has()`, or `bound()`. The container exposes only `bind()`, `singleton()`, `instance()`, and `make()` — use `make()` / `app(X::class)` to resolve.

## Notes

Only type-hinted, non-primitive parameters are auto-resolved. A scalar parameter (e.g. `string $name`) with no default value cannot be injected and will throw — give it a default or resolve it manually. Middleware is constructed with `new` (not the container), so middleware constructors should take no required arguments.

## Tips

Bind interfaces to implementations in a provider so `app(MyInterface::class)` returns the concrete class everywhere.
