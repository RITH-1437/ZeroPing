# ZeroPing Packages

ZeroPing has a first-class package system. A package is a Composer package that
declares one or more **service providers** under `extra.zeroping.providers`.
Packages are auto-discovered at boot, so installing a package is just a
Composer require (or dropping it into `packages/`) — no core edits required.

## Anatomy of a package

```
packages/<vendor>/<name>/
    composer.json        # name, psr-4 autoload, extra.zeroping.providers
    src/
        <Name>ServiceProvider.php
    routes/              # route files loaded via loadRoutesFrom()
    config/             # config files merged via mergeConfigFrom()
    database/
        migrations/     # migrations loaded via loadMigrationsFrom()
    views/              # namespaced views via loadViewsFrom()
    assets/             # publishable assets
    tests/
    README.md
```

A minimal `composer.json`:

```json
{
    "name": "zeroping/blog",
    "type": "library",
    "require": { "php": ">=8.1" },
    "autoload": { "psr-4": { "Zeroping\\Blog\\": "src/" } },
    "extra": {
        "zeroping": { "providers": ["Zeroping\\Blog\\BlogServiceProvider"] }
    }
}
```

## The service provider

Every package extends `Zeroping\Support\ServiceProvider` and implements
`register()` and/or `boot()`:

```php
namespace Zeroping\Blog;

use Zeroping\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge this package's config (without overwriting host values).
        $this->mergeConfigFrom(__DIR__ . '/../config/blog.php', 'blog');

        // Bind services into the container.
        $this->container->singleton(BlogManager::class, fn () => new BlogManager());
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadViewsFrom(__DIR__ . '/../views', 'blog');
        $this->commands([BlogControllerCommand::class]);
        $this->publishes([
            __DIR__ . '/../config/blog.php' => base_path('config/blog.php'),
        ], 'blog-config');
    }
}
```

| Method | Purpose |
|---|---|
| `mergeConfigFrom($path, $key)` | Merge package config into the host repository |
| `loadRoutesFrom($path)` | Require a routes file that calls `Router::get/post/...` |
| `loadMigrationsFrom($path)` | Register a migration directory |
| `loadViewsFrom($path, $ns)` | Register a namespaced view (`view('blog::index')`) |
| `commands([...])` | Register console commands (via the `CommandRegistry`) |
| `publishes([from=>to], $group)` | Declare assets for `package:publish` |

## Auto-discovery

On every `composer dump-autoload` the `post-autoload-dump` hook
(`scripts/discover-packages.php`) scans:

- `packages/*/*/composer.json` (first-party packages in the repo), and
- `vendor/composer/installed.json` (distributed packages)

for `extra.zeroping.providers`, and writes the resolved manifest to
`bootstrap/cache/packages.php`. At boot, `App::registerProviders()` merges
the discovered providers into `config/app.php`'s `providers` — unless
auto-discovery is off (see below).

No package names are hard-coded: discovery is driven entirely by the
`extra.zeroping.providers` declarations.

## Enabling / disabling

`config/packages.php` is the single source of truth for a package's state:

```php
return [
    'zeroping/support' => true,   // enabled
    'zeroping/queue'   => false,  // disabled
];
```

Omit a package to fall back to the `PACKAGE_AUTO_DISCOVER` flag (enabled
by default). Set the env var to `false` to disable discovery entirely:

```env
PACKAGE_AUTO_DISCOVER=false
```

Run `php zero package:list` to see the resolved state of every discovered
package, split into **Enabled** and **Disabled**.

## Installing a package

For first-party packages already under `packages/`, discovery picks them up
automatically after `composer dump-autoload`. For distributed packages:

```bash
composer require zeroping/blog
php zero package:publish zeroping/blog
php zero migrate
```

`package:publish` copies the package's declared assets (config, views,
migrations, etc.) into the host application.

## Package lifecycle

1. **Discovered** — declared in `composer.json` (`extra.zeroping.providers`).
2. **Enabled/Disabled** — resolved from `config/packages.php` + the
   `PACKAGE_AUTO_DISCOVER` flag.
3. **Registered** — `register()` runs (bind services into the container).
4. **Booted** — `boot()` runs (routes, migrations, views, commands,
   publishable assets).
5. **Published** — `package:publish` copies assets into the host app.
6. **Removed** — `composer remove` + `composer dump-autoload` drops
   the provider from the manifest; cached assets can be removed manually.

## Starter Kits (roadmap)

To install a complete, opinionated stack in one command instead of adding
packages one by one, a future `starter:install` command will resolve a
named bundle (e.g. `arena`, `ecommerce`, `api`) and enable/configure the
required packages automatically.
