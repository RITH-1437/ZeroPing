# Performance

ZeroPing includes several performance optimization features to speed up your application in production.

## Route Caching

Route caching compiles all registered routes into a single optimized file, eliminating the need to parse `config/routes.php` on every request.

```bash
php zero route:cache
```

To clear the route cache:

```bash
php zero route:clear
```

## Configuration Caching

Configuration caching merges all config files into a single array and writes it to a cached file. This avoids repeated `require` calls and file I/O on every request.

```bash
php zero config:cache
```

To clear the config cache:

```bash
php zero config:clear
```

## View Caching

Compiled views are stored in `storage/cache/views/`. Once cached, views are served directly from the pre-compiled file without re-parsing the template.

```bash
php zero view:cache
```

To clear the view cache:

```bash
php zero view:clear
```

## Optimize Command

The `optimize` command runs all caching operations at once:

```bash
php zero optimize
```

This runs:

1. `config:cache` — Cache configuration
2. `route:cache` — Cache routes
3. `view:cache` — Cache compiled views
4. `search:index` — Build search index

To clear everything:

```bash
php zero optimize:clear
```

## Lazy Service Loading

Services are loaded lazily through the container. A service provider is only instantiated and registered when one of its services is first requested. This means:

- Unused services consume zero memory
- Request time is not spent booting providers that won't be used
- Only the core routing and config systems are loaded eagerly

## Best Practices

1. **Run `php zero optimize` in deployment** for maximum performance
2. **Keep config files minimal** — only load what you need
3. **Use route caching** in production environments
4. **Cache views** for any pages rendered more than once
5. **Build the search index** on deploy, not on first user request
