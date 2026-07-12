# Caching

Caching lives in `App\Core\Cache`: a `CacheManager` (selects a store), a `CacheRepository` (the object you call), and a static `Cache` facade. Three drivers ship: `file`, `array`, and `null`.

## Reading & writing

```php
use App\Core\Cache\Cache;

// Write (TTL in SECONDS, required)
Cache::put('user:1', $user, 600);

// Read (returns default if missing)
$user = Cache::get('user:1');
$user = Cache::get('user:1', null);

// Existence
if (Cache::has('user:1')) { /* ... */ }

// Remember: compute once, then cache for the TTL
$posts = Cache::remember('posts:recent', 300, fn () => Post::latest()->get());
$config = Cache::rememberForever('app.config', fn () => loadConfig());
Cache::forever('app.name', 'ZeroPing');

// Delete / clear
Cache::forget('user:1');
Cache::flush();
```

## The cache() helper

```php
cache(['name' => 'ZeroPing'], 300);  // put with 300s TTL
$name = cache('name');               // get
$manager = cache();                  // CacheManager instance
$arrayStore = cache()->store('array');
```

## Incrementing counters

```php
Cache::increment('visits');          // +1
Cache::increment('visits', 5);       // +5
Cache::decrement('quota');
```

## Configuration

`config/cache.php` defines the default store (`file`) and the available stores: `file` (path `storage/cache`), `array` (per-request, in-memory), and `null` (no-op). Select a store with `Cache::store('array')` or via the manager.

## Best Practices

Use `remember()` around expensive queries and render steps. Wrap cached values in a short TTL so stale data expires automatically.

## Common Mistakes

Calling `Cache::set()` or `Cache::delete()`. The methods are `put()` (not `set()`) and `forget()` (not `delete()`). The TTL is in **seconds**, and there is no `Cache::get('key', 60)` expiry argument.

## Notes

The `lifetime` key in `config/cache.php` is not read — the TTL is always passed explicitly to `put()` / `remember()`. The `file` driver stores JSON files under `storage/cache`; the `array` driver is lost at the end of the request.

## Tips

Clear the cache from the CLI with `php zero cache:clear` when you need to invalidate everything at once.
