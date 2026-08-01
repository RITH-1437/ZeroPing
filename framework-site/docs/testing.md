# Testing

ZeroPing ships with a full testing suite built on **PHPUnit**. The framework provides a base `TestCase` class, HTTP testing utilities, and database testing helpers that let you test everything from isolated units to full HTTP request/response cycles.

---

## Setup

Testing is configured in `phpunit.xml` at the project root. No additional packages are required.

```bash
# Run the full test suite
php zero test

# Run a specific test class
php zero test --filter=UserServiceTest

# Run a specific test suite
php zero test --testsuite=Feature

# Run directly with PHPUnit
vendor/bin/phpunit
```

---

## Test structure

```
tests/
├── bootstrap.php        # Autoloader + test environment constants
├── TestCase.php         # Base class used by all tests
├── Feature/             # HTTP and integration tests
└── Unit/                # Pure unit tests (no HTTP/DB)
```

Convention: mirror `app/` structure inside `tests/Unit/` (e.g., `app/Services/UserService.php` → `tests/Unit/Services/UserServiceTest.php`).

---

## TestCase base class

All test classes extend `Tests\TestCase`:

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;

class MyTest extends TestCase
{
    public function test_something(): void
    {
        // $this->container is a fresh Container for every test
    }
}
```

### What TestCase provides

| Method | Purpose |
|---|---|
| `$this->container` | Fresh `Container` instance, reset per test |
| `setRequestMethod(string $method)` | Set `$_SERVER['REQUEST_METHOD']` |
| `setRequestUri(string $uri)` | Set `$_SERVER['REQUEST_URI']` and `QUERY_STRING` |
| `setRequestHeaders(array $headers)` | Set `$_SERVER['HTTP_*']` headers |
| `setFormRequestBody(array $data)` | Populate `$_POST` with form data |
| `setJsonRequestBody(array $data)` | Set JSON content type header |
| `withSessionData(array $data)` | Pre-populate `$_SESSION` |
| `captureOutput(callable $cb)` | Capture anything echoed inside the callback |

`tearDown()` automatically resets `$_GET`, `$_POST`, `$_SERVER`, and `$_SESSION` after every test. You do not need to clean up superglobals manually.

---

## Unit tests

Unit tests exercise a single class in isolation. Use PHPUnit mocks or manual fakes for dependencies.

```php
<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\OrderService;
use App\Repositories\OrderRepository;
use App\Jobs\SendOrderConfirmationJob;

class OrderServiceTest extends TestCase
{
    public function test_creates_order_and_dispatches_confirmation(): void
    {
        // Arrange
        $repo = $this->createMock(OrderRepository::class);
        $repo->expects($this->once())
            ->method('create')
            ->willReturn((object)['id' => 42, 'total' => 99.00]);

        $this->container->instance(OrderRepository::class, $repo);

        // Act
        $service = $this->container->make(OrderService::class);
        $order   = $service->place(['product_id' => 1, 'quantity' => 2]);

        // Assert
        $this->assertEquals(42, $order->id);
    }

    public function test_throws_on_out_of_stock(): void
    {
        $this->expectException(\App\Exceptions\OutOfStockException::class);

        $service = $this->container->make(OrderService::class);
        $service->place(['product_id' => 999, 'quantity' => 100]);
    }
}
```

---

## HTTP / Feature tests

Feature tests boot the full application and simulate HTTP requests.

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Core\Application\App;

class PostApiTest extends TestCase
{
    private function request(string $method, string $uri, array $body = []): string
    {
        $this->setRequestMethod($method);
        $this->setRequestUri($uri);
        $this->setRequestHeaders(['Accept' => 'application/json']);

        if ($body) {
            $this->setFormRequestBody($body);
        }

        return $this->captureOutput(function () {
            (new App(BASE_PATH))->handle();
        });
    }

    public function test_index_returns_posts(): void
    {
        $output = $this->request('GET', '/api/v1/posts');
        $data   = json_decode($output, true);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
    }

    public function test_create_requires_authentication(): void
    {
        $this->request('POST', '/api/v1/posts', ['title' => 'Hello']);

        $this->assertEquals(401, http_response_code());
    }

    public function test_create_validates_title(): void
    {
        $this->withSessionData(['user_id' => 1]);
        $output = $this->request('POST', '/api/v1/posts', ['title' => '']);
        $errors = json_decode($output, true);

        $this->assertArrayHasKey('errors', $errors);
        $this->assertArrayHasKey('title', $errors['errors']);
    }
}
```

---

## Database testing

### SQLite in-memory

For fast, isolated database tests set `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` in `.env.testing` or `phpunit.xml`:

```xml
<!-- phpunit.xml -->
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

Run migrations before each test class:

```php
class DatabaseTestCase extends TestCase
{
    protected static bool $migrated = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$migrated) {
            (new App(BASE_PATH))->handle(); // boots providers
            \App\Core\Database\MigrationRunner::run(BASE_PATH . '/database/migrations');
            static::$migrated = true;
        }
    }
}
```

### Transaction rollback

Wrap each test in a database transaction so changes never persist between tests:

```php
class TransactionalTestCase extends DatabaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        \App\Core\Database\Database::connection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        \App\Core\Database\Database::connection()->rollBack();
        parent::tearDown();
    }
}
```

### Database assertions

```php
// Assert a record exists
$this->assertDatabaseHas('users', ['email' => 'alice@example.com']);

// Assert a record does not exist
$this->assertDatabaseMissing('users', ['email' => 'deleted@example.com']);

// Assert a count
$this->assertEquals(3, \App\Models\Post::count());
```

You can implement these helpers in your own `DatabaseTestCase`:

```php
protected function assertDatabaseHas(string $table, array $where): void
{
    $qb    = \App\Core\Database\Database::table($table);
    foreach ($where as $col => $val) {
        $qb->where($col, $val);
    }
    $this->assertNotNull($qb->first(), "Row not found in [{$table}].");
}

protected function assertDatabaseMissing(string $table, array $where): void
{
    $qb    = \App\Core\Database\Database::table($table);
    foreach ($where as $col => $val) {
        $qb->where($col, $val);
    }
    $this->assertNull($qb->first(), "Unexpected row found in [{$table}].");
}
```

---

## Mocking and fakes

### Container-based swapping

The cleanest way to inject a fake: register it in the container before making the class under test.

```php
// Fake mailer
$fakeMailer = new class {
    public array $sent = [];
    public function to(string $addr): static { return $this; }
    public function send(object $mailable): void { $this->sent[] = $mailable; }
};

$this->container->instance(\App\Core\Mail\Mailer::class, $fakeMailer);

$service = $this->container->make(\App\Services\WelcomeService::class);
$service->sendWelcome('alice@example.com');

$this->assertCount(1, $fakeMailer->sent);
$this->assertInstanceOf(\App\Mail\WelcomeMail::class, $fakeMailer->sent[0]);
```

### PHPUnit mocks

Standard PHPUnit mock objects work with any interface or concrete class:

```php
$cache = $this->createMock(\App\Core\Cache\CacheRepository::class);
$cache->method('get')->with('user:1')->willReturn(['id' => 1, 'name' => 'Alice']);
$cache->expects($this->once())->method('put');

$this->container->instance(\App\Core\Cache\CacheRepository::class, $cache);
```

### Spy / recording fake

```php
class SpyQueue
{
    public array $dispatched = [];

    public function dispatch(object $job): void
    {
        $this->dispatched[] = $job;
    }
}

$spy = new SpyQueue();
$this->container->instance(\App\Core\Queue\QueueManager::class, $spy);

app(OrderService::class)->place($data);

$this->assertCount(1, $spy->dispatched);
$this->assertInstanceOf(ProcessPaymentJob::class, $spy->dispatched[0]);
```

---

## Testing validation

```php
use App\Core\Validation\Validator;

class ValidationTest extends TestCase
{
    public function test_email_rule(): void
    {
        $result = Validator::make(
            ['email' => 'not-an-email'],
            ['email' => 'required|email']
        );

        $this->assertTrue($result->fails());
        $this->assertArrayHasKey('email', $result->errors());
    }

    public function test_passes_with_valid_data(): void
    {
        $result = Validator::make(
            ['email' => 'alice@example.com'],
            ['email' => 'required|email']
        );

        $this->assertFalse($result->fails());
    }
}
```

---

## Testing console commands

```php
use App\Console\Commands\SendDigestCommand;

class SendDigestCommandTest extends TestCase
{
    public function test_sends_digest_to_active_subscribers(): void
    {
        // Seed some subscribers
        Subscriber::create(['email' => 'a@test.com', 'active' => true]);
        Subscriber::create(['email' => 'b@test.com', 'active' => false]);

        $fakeMailer = new FakeMailer();
        $this->container->instance(\App\Core\Mail\Mailer::class, $fakeMailer);

        $output = $this->captureOutput(function () {
            (new SendDigestCommand())->handle();
        });

        $this->assertCount(1, $fakeMailer->sent);  // only 1 active subscriber
        $this->assertStringContainsString('1 subscribers', $output);
    }
}
```

---

## Code coverage

```bash
# HTML coverage report (requires Xdebug or PCOV)
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html coverage/

# Text summary
XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text
```

---

## Continuous integration

`.github/workflows/` contains a CI workflow that runs PHPUnit, PHPStan, and PHP_CodeSniffer on every push and pull request. Ensure all three pass before opening a PR.
