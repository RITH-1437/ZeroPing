# Configuration

Configuration is loaded from `config/*.php` files and accessed via the
`config()` helper.

```php
// Read a value (with a default)
$timezone = config('app.timezone', 'UTC');

// Write a runtime value
config()->set('app.debug', true);
```

Registered configuration files include:

- `config/app.php` — application name, version, providers.
- `config/database.php` — connections and default driver.
- `config/cache.php` — cache driver and TTL defaults.
- `config/session.php` — session driver and lifetime.
