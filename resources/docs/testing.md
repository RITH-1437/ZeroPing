# Testing

ZeroPing ships with a PHPUnit-compatible testing harness in `App\Core\Testing`.

```php
use App\Core\Testing\TestCase;

class ExampleTest extends TestCase
{
    public function test_it_works(): void
    {
        $response = $this->get('/docs/introduction');
        $response->assertOk();
        $response->assertSee('Installation');
    }
}
```

The harness provides:

- `get()`, `post()` and other HTTP verbs via `MakesHttpRequests`.
- `assertSee()`, `assertOk()`, `assertStatus()` via `TestResponse`.
- Database assertions via `DatabaseAssertions`.
