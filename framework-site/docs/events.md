# Events

ZeroPing's event system decouples the parts of your application that emit notifications from the parts that act on them. An **event** is a plain data-carrying object. A **listener** is a class that responds to that event. The `EventDispatcher` wires them together and calls listeners in priority order.

## Core Concepts

- **Event** — a class that extends `App\Core\Events\Event` and holds the data relevant to what happened.
- **Listener** — a class that implements `App\Core\Events\Listener` and defines a `handle(Event $event)` method.
- **EventDispatcher** — matches events to listeners and invokes them. Listeners can halt propagation by calling `$event->stopPropagation()`.

## Creating an Event

```bash
php zero make:event UserRegistered
```

This generates `app/Events/UserRegisteredEvent.php`. Add the data your listeners will need as public properties or constructor arguments:

```php
<?php

namespace App\Events;

use App\Core\Events\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
```

## Creating a Listener

```bash
php zero make:listener SendWelcomeEmail
```

This generates `app/Listeners/SendWelcomeEmailListener.php`:

```php
<?php

namespace App\Listeners;

use App\Core\Events\Event;
use App\Core\Events\Listener;
use App\Events\UserRegisteredEvent;
use App\Mail\WelcomeEmail;
use App\Core\Mail\MailManager;

class SendWelcomeEmailListener implements Listener
{
    public function handle(Event $event): void
    {
        /** @var UserRegisteredEvent $event */
        $mailer = app(MailManager::class);
        $mailer->send(
            (new WelcomeEmail())->to($event->email)
        );
    }
}
```

## Registering Listeners

Register listeners in your `EventServiceProvider` (or directly in your bootstrap file) by calling `$dispatcher->listen()`:

```php
<?php

namespace App\Providers;

use App\Core\Events\EventDispatcher;
use App\Events\UserRegisteredEvent;
use App\Listeners\SendWelcomeEmailListener;
use App\Listeners\CreateDefaultWorkspaceListener;
use App\Listeners\NotifyAdminListener;

class EventServiceProvider
{
    public function register(EventDispatcher $dispatcher): void
    {
        // Higher priority runs first (default is 0)
        $dispatcher->listen(
            UserRegisteredEvent::class,
            SendWelcomeEmailListener::class,
            priority: 10
        );

        $dispatcher->listen(
            UserRegisteredEvent::class,
            CreateDefaultWorkspaceListener::class,
            priority: 5
        );

        $dispatcher->listen(
            UserRegisteredEvent::class,
            NotifyAdminListener::class,
            priority: 0
        );
    }
}
```

## Dispatching Events

Dispatch an event by calling `dispatch()` on the `EventDispatcher` instance, or use the global `dispatch()` helper if you have wrapped the call in a job. Resolve the dispatcher from the container:

```php
use App\Core\Events\EventDispatcher;
use App\Events\UserRegisteredEvent;

$dispatcher = app(EventDispatcher::class);
$dispatcher->dispatch(new UserRegisteredEvent(
    userId: $user['id'],
    email:  $user['email'],
    name:   $user['name'],
));
```

Or bind the dispatcher in a service and inject it via the constructor:

```php
class RegisterController extends Controller
{
    public function __construct(
        private readonly EventDispatcher $events,
    ) {}

    public function register(): void
    {
        $user = $this->createUser($_POST);

        $this->events->dispatch(new UserRegisteredEvent(
            userId: $user['id'],
            email:  $user['email'],
            name:   $user['name'],
        ));

        redirect('/dashboard');
    }
}
```

## Stopping Propagation

A listener can prevent subsequent listeners from running by calling `stopPropagation()` on the event:

```php
public function handle(Event $event): void
{
    if ($this->isBanned($event->email)) {
        $event->stopPropagation();
        return;
    }

    // ...
}
```

## Event Payload Patterns

### Single model or ID

Keep payloads light. Pass the model ID and let each listener load what it needs:

```php
class OrderPlacedEvent extends Event
{
    public function __construct(public readonly int $orderId) {}
}
```

### Value object payload

Pass a value object when multiple listeners need the same set of fields:

```php
class PasswordResetRequestedEvent extends Event
{
    public function __construct(
        public readonly string $email,
        public readonly string $token,
        public readonly \DateTimeImmutable $expiresAt,
    ) {}
}
```

## Checking for Listeners

```php
$dispatcher = app(EventDispatcher::class);

if ($dispatcher->hasListeners(UserRegisteredEvent::class)) {
    // ...
}
```

## Removing Listeners

```php
// Remove all listeners for a specific event
$dispatcher->forget(UserRegisteredEvent::class);

// Remove all listeners for all events
$dispatcher->forget();
```

## Complete Example: UserRegistered Event

### The event

```php
<?php

namespace App\Events;

use App\Core\Events\Event;

class UserRegisteredEvent extends Event
{
    public function __construct(
        public readonly int    $userId,
        public readonly string $email,
        public readonly string $name,
    ) {}
}
```

### The listener

```php
<?php

namespace App\Listeners;

use App\Core\Events\Event;
use App\Core\Events\Listener;
use App\Events\UserRegisteredEvent;

class SendWelcomeEmailListener implements Listener
{
    public function handle(Event $event): void
    {
        /** @var UserRegisteredEvent $event */
        logger("Sending welcome email to {$event->email}");

        // Send mail ...
    }
}
```

### Registration

```php
$dispatcher->listen(
    UserRegisteredEvent::class,
    SendWelcomeEmailListener::class,
);
```

### Dispatch from controller

```php
$dispatcher->dispatch(new UserRegisteredEvent(
    userId: $newUser['id'],
    email:  $newUser['email'],
    name:   $newUser['name'],
));
```

## Tips

- Events are synchronous by default. For long-running work (sending emails, processing images), dispatch a queued job from inside the listener rather than doing the work inline.
- Prefer descriptive past-tense names for events: `UserRegistered`, `OrderPlaced`, `PaymentFailed`.
- Keep events immutable (use `readonly` properties). Mutating an event inside a listener and relying on that mutation in a later listener creates hidden coupling.
