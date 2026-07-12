# Extending ZeroPing

ZeroPing is designed to be extended at every layer — from service registration and event-driven logic to custom drivers and middleware.

## Service Providers

Providers are the primary extension mechanism. They live in `app/Providers/` and are registered in `config/app.php`.

```php
namespace App\Providers;

use App\Core\Container\Container;

class MyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->container->singleton(MyService::class, function () {
            return new MyService();
        });
    }

    public function boot(): void
    {
        $service = $this->container->resolve(MyService::class);
        $service->init();
    }
}
```

Every provider receives the `Container` on construction. Use `register()` to bind services, and `boot()` to run logic after all providers are registered.

## Events & Listeners

Events decouple your application logic. Define an event class, attach listeners in a Service Provider, and dispatch when the action occurs.

```php
// app/Events/OrderShipped.php
namespace App\Events;

use App\Core\Events\Event;

class OrderShipped extends Event
{
    public function __construct(public array $order) {}
}

// app/Listeners/SendShipmentNotification.php
namespace App\Listeners;

use App\Core\Events\Listener;
use App\Core\Events\Event;

class SendShipmentNotification extends Listener
{
    public function handle(Event $event): void
    {
        // $event->order ...
    }
}

// In a ServiceProvider:
$dispatcher = $this->container->resolve(\App\Core\Events\EventDispatcher::class);
$dispatcher->listen(OrderShipped::class, SendShipmentNotification::class);

// Dispatch anywhere:
$dispatcher->dispatch(new OrderShipped($orderData));
```

## Middleware

Middleware sits between the incoming request and your controller. Create a class that extends `App\Http\Middleware\Middleware` and register it in `config/routes.php` or `config/app.php`.

```php
namespace App\Http\Middleware;

class CorsMiddleware extends Middleware
{
    public function handle(): void
    {
        header('Access-Control-Allow-Origin: *');
    }
}
```

## Custom Drivers

Cache, Queue, Mail, and Filesystem all use the driver pattern. Implement the corresponding driver interface and register it via a Service Provider:

- **Cache**: implement `App\Core\Cache\Drivers\CacheDriver`
- **Queue**: implement `App\Core\Queue\Drivers\QueueDriver`
- **Mail**: implement `App\Core\Mail\Drivers\MailDriver`
- **Filesystem**: implement `App\Core\Filesystem\Drivers\FilesystemDriver`

```php
use App\Core\Cache\Drivers\CacheDriver;

class RedisCacheDriver implements CacheDriver
{
    public function get(string $key): mixed { /* ... */ }
    public function set(string $key, mixed $value, int $ttl = 0): void { /* ... */ }
    public function delete(string $key): void { /* ... */ }
    public function clear(): void { /* ... */ }
}
```

## Custom Validation Rules

Add your own rules by extending `App\Core\Validation\Rules\Rule` and registering it via `RuleRegistry`.

```php
use App\Core\Validation\Rules\Rule;
use App\Core\Validation\RuleRegistry;

class UppercaseRule extends Rule
{
    public function validate(mixed $value): bool
    {
        return strtoupper($value) === $value;
    }

    public function message(): string
    {
        return 'The :field must be uppercase.';
    }
}

// Register:
RuleRegistry::add('uppercase', UppercaseRule::class);
```

## Container Bindings

The `Container` lets you bind interfaces to concrete implementations, making it easy to swap components.

```php
$container->bind(CacheDriver::class, RedisCacheDriver::class);
$container->singleton(MailDriver::class, fn() => new LogDriver);
```

Always register providers and middleware in `config/app.php`. This keeps your application bootstrap predictable and testable.
