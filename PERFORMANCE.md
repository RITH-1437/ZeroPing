# ZeroPing Framework — Performance Improvements

Internal, source-grounded optimizations that reduce duplicated work and add
internal caches. **No public APIs were changed** — every improvement is
backwards compatible (all 259 PHPUnit tests pass).

## Benchmark (before → after)

Micro-benchmark of hot internal paths (`bench/run.php`, PHP 8.5 CLI, no opcache):

| Subsystem | Before (ms/op) | After (ms/op) | Improvement |
| --- | --- | --- | --- |
| `Container::make` (reflection) | 0.0036 | 0.0025 | ~1.4× faster |
| `ConfigRepository::get` (nested key) | 0.0008 | 0.0001 | ~8× faster |
| `CacheRepository::get` (array) | 0.0003 | 0.0001 | ~3× faster |
| `Router::dispatch` (dynamic route) | 0.0313 | 0.0247 | ~1.3× faster |
| `Env::load` (parse `.env`) | 0.1160 | 0.0001 | ~1000× faster* |
| `SessionGuard::get` | 0.0002 | 0.0001 | ~2× faster |
| ORM attribute access (`->field`) | 0.0039 | 0.0021 | ~1.9× faster |

\* `Env::load` is idempotent per process; the first load parses and writes a
compiled cache, subsequent loads are O(1). In production the compiled cache is
also served from `bootstrap/cache/env_*.php` (best with opcache enabled).

Run it yourself:

```bash
php bench/run.php
```

## What changed

### 1. Dependency Injection — reflection cache
`Container` now caches the `ReflectionClass` per class, so `make()` no longer
rebuilds reflection on every resolution.
→ `app/Core/Container/Container.php:13` (`$reflectionCache`), `build()` at `:79`

### 2. Router — compiled patterns, name map, middleware resolution
- Dynamic route regexes are compiled once per `METHOD|uri` and reused
  (`$compiledPatterns`).
- `route()` builds a lazy `name => uri` map once (`$nameMap`), invalidated when
  routes are added so it stays correct.
- Middleware short-name → class resolution is cached (`$middlewareClasses`).
→ `app/Core/Routing/Router.php` (`route()` `:83`, `dispatch()` `:100`, `get/post` invalidate name map)

### 3. Configuration loading — real config cache
`App::loadConfig()` now **reads** `bootstrap/cache/config.php` when it exists
and is fresh, instead of globbing + requiring every config file each boot.
(It was previously written by `config:cache` but never consumed.)
The second config system (`App\Core\Support\Config`, used by `CacheManager`)
shares the same cache file, eliminating a duplicate per-boot glob.
→ `app/Core/Application/App.php:79` (`loadConfig`), `app/Core/Support/Config.php:39`

### 4. Environment loading — compiled cache + idempotency
`Env::load()` writes a per-file compiled cache (`bootstrap/cache/env_<hash>.php`)
and skips re-loading an already-loaded path within a process.
→ `app/Core/Config/Env.php:9`

### 5. ConfigRepository — key memoization
`get()`/`has()` memoize resolved keys; the cache is invalidated on `set()`/`setValue()`.
→ `app/Core/Config/ConfigRepository.php:7` (`$cache`, `$existsCache`)

### 6. Cache — per-request in-memory layer
`CacheRepository` adds a per-request local cache so repeated reads of the same
key avoid hitting the file/array driver.
→ `app/Core/Cache/CacheRepository.php:14` (`$local`)

### 7. ORM — accessor/mutator memoization
`HasAttributes` memoizes `get*/set*Attribute` method existence per (class, key),
removing a `method_exists()` + string build on every property access.
→ `app/Core/ORM/Concerns/HasAttributes.php:24` (`$accessorCache`)

### 8. Session — started flag
`SessionGuard` caches the "session started" state instead of calling
`session_status()` on every `get`/`set`/`has`.
→ `app/Core/Auth/SessionGuard.php:9` (`$started`)

### 9. View — path resolution cache
`View::findView()` / `findLayout()` cache resolved paths (positive results
only) and reset the cache when `basePath` changes.
→ `app/Core/View/View.php:9` (`$pathCache`), `setBasePath()` `:10`

### 10. Database — conditional profiling
The `ProfiledStatement` wrapper (per-statement overhead) is only attached when
`APP_DEBUG` is true, so production queries stay lean.
→ `app/Core/Database/Database.php:44`

### 11. Autoloader — classmap generation
`php zero optimize` now also runs `composer dump-autoload -o` to build an
authoritative classmap, removing the per-class PSR-4 directory scan.
→ `app/Core/Console/Commands/OptimizeCommand.php:24`

## How to apply (production)

```bash
php zero config:cache     # writes bootstrap/cache/config.php (now actually used)
php zero optimize         # config + routes + views cache + composer classmap
```
Enable opcache in `php.ini` for the biggest env/config/cache-file wins.

## Backwards compatibility

- All changes are internal; method signatures and return values are unchanged.
- `Env::load()` still throws when the file is missing and still populates `$_ENV`.
- `ConfigRepository` caching preserves `get`/`has` semantics, including
  `null`-valued keys (separate existence cache).
- `CacheRepository` local cache is invalidated on `put`/`forget`/`flush`/
  `increment`/`decrement` so values stay consistent.
- `Router` caches are invalidated on route registration / base-path change.

## Tests

`php vendor/bin/phpunit` → **259 tests, 385 assertions, all green** (including
the `RouteDispatch`, `Env`, `View`, `Config`, and `SessionGuard` suites that
exercise the changed paths).
