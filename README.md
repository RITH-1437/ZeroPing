<p align="center">
  <a href="https://zero-ping.duckdns.org">
    <img src="https://raw.githubusercontent.com/rith-1437/ZeroPing/main/public/assets/images/mascot.svg" alt="ZeroPing" width="120">
  </a>
</p>

<h1 align="center">ZeroPing</h1>

<p align="center">
  <strong>Clean. Expressive. Familiar.</strong><br>
  A modern PHP framework built from scratch.
</p>

<p align="center">
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/v/rith-1437/zeroping.svg?style=flat-square&color=14B8A6" alt="Latest Stable Version"></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square" alt="PHP >= 8.1">
  <img src="https://img.shields.io/badge/PHP%20Matrix-8.1%20%7C%208.2%20%7C%208.3%20%7C%208.4%20%7C%208.5-8892BF.svg?style=flat-square" alt="PHP Matrix">
  <a href="https://github.com/rith-1437/ZeroPing/blob/main/LICENSE"><img src="https://img.shields.io/github/license/rith-1437/ZeroPing?style=flat-square" alt="License"></a>
  <a href="https://github.com/rith-1437/ZeroPing/actions"><img src="https://img.shields.io/github/actions/workflow/status/rith-1437/ZeroPing/ci.yml?style=flat-square" alt="Tests"></a>
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/dt/rith-1437/zeroping.svg?style=flat-square" alt="Downloads"></a>
</p>

---

## Introduction

ZeroPing is a lightweight, modern PHP framework built entirely from scratch — no third-party runtime dependencies, no configuration required to get started, and no hidden magic you can't trace. It ships with a clean MVC architecture, a fast dependency-injection container, an expressive Active Record ORM, fluent validation, multi-driver caching, a background queue, a task scheduler, and developer-friendly CLI tooling — all written by hand, all in one place.

The philosophy is simple: a framework should feel familiar, stay out of your way, and give you the tools to build anything without pulling in a forest of packages. ZeroPing is designed to be readable, hackable, and enjoyable to work with at any scale.

It is free, open-source, and created by **Rin Nairith**.

---

## What makes ZeroPing different?

**Zero third-party runtime dependencies.**
The entire framework — routing, ORM, DI container, validation, cache, queue, scheduler, encryption, and more — is implemented from scratch. Your production bundle carries no surprise transitive packages.

**Zero configuration to run.**
Clone the repo, run `php zero serve`, and you have a working application on `localhost:1437`. Sane defaults are baked in; you only configure what you intentionally want to change.

**MVC + DI + ORM, all from scratch.**
Most "lightweight" frameworks still delegate their ORM or container to a third-party library. ZeroPing doesn't. Every layer is purpose-built, coherent, and consistent in style.

**A full developer experience out of the box.**
Code generation, a debug toolbar, route listing, environment verification, and a built-in development server are all available via the `php zero` CLI — no separate toolchain required.

---

## Features

| Category | Features |
|----------|----------|
| **Architecture** | MVC, Dependency Injection, Service Providers, Middleware Pipeline |
| **Routing** | Static & Dynamic Routes, Route Groups, Prefixes, Named Routes |
| **Database** | ORM with Relationships, Query Builder, Migrations, Seeding |
| **Validation** | Fluent Validator, FormRequest, 20+ Built-in Rules |
| **Security** | CSRF Protection, Encryption, Rate Limiting, Hashing |
| **Performance** | File/Array/Database Cache, Route Caching, Config Caching |
| **Background** | Queue with Sync/Database Drivers, Task Scheduler |
| **Developer Experience** | CLI Tooling, Starter Templates, Debug Toolbar, Logging |
| **Testing** | PHPUnit Integration, HTTP Assertions, Database Transactions |

---

## Installation

**Zero CLI** (recommended):

```bash
php zero new my-app
cd my-app
php zero serve
```

**Composer**:

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

Open [http://localhost:1437](http://localhost:1437).

---

## Quick Start

**1. Define a route** (`config/routes.php`):

```php
use App\Core\Routing\Router;
use App\Controllers\GreetingController;

Router::get('/hello', [GreetingController::class, 'index']);
```

**2. Create a controller** (`app/Controllers/GreetingController.php`):

```php
<?php

namespace App\Controllers;

use App\Core\View\Controller;

class GreetingController extends Controller
{
    public function index(): string
    {
        return view('greeting', [
            'name' => config('app.name'),
        ]);
    }
}
```

**3. Create a view** (`views/greeting.php`):

```php
<h1>Hello from <?= e($name) ?>!</h1>
```

Start the server and visit your page:

```bash
php zero serve
# open http://localhost:1437/hello
```

---

## CLI

ZeroPing ships with a powerful `php zero` CLI for code generation and project management.

**Project**

```bash
php zero serve              # Start the development server (default: port 1437)
php zero serve 8080         # Start on a custom port
php zero about              # Display framework info and environment summary
php zero doctor             # Verify environment requirements
php zero route:list         # List all registered routes with methods and names
```

**Code Generation**

```bash
php zero make:controller PostController       # Generate a controller
php zero make:model Post                      # Generate a model
php zero make:migration create_posts_table    # Generate a migration file
php zero make:seeder PostSeeder               # Generate a database seeder
php zero make:command SendEmailsCommand       # Generate a console command
php zero make:test PostControllerTest         # Generate a PHPUnit test class
php zero make:auth                            # Scaffold authentication (login, register, etc.)
```

**Database**

```bash
php zero migrate            # Run pending migrations
php zero migrate:rollback   # Roll back the last batch of migrations
php zero migrate:fresh      # Drop all tables and re-run all migrations
php zero db:seed            # Run database seeders
```

**Utilities**

```bash
php zero publish            # Publish framework assets for customization
php zero new my-app         # Create a new ZeroPing project
```

---

## Documentation

Full documentation is available at [zero-ping.duckdns.org](https://zero-ping.duckdns.org):

| Page | Description |
|------|-------------|
| [Introduction](https://zero-ping.duckdns.org/docs/introduction) | What ZeroPing is and why it exists |
| [Installation](https://zero-ping.duckdns.org/installation) | Install and configure ZeroPing |
| [Getting Started](https://zero-ping.duckdns.org/getting-started) | Build your first app |
| [CLI Reference](https://zero-ping.duckdns.org/docs/cli) | Complete CLI command reference |
| [Database & ORM](https://zero-ping.duckdns.org/docs/database) | Models, relationships, migrations |
| [Validation](https://zero-ping.duckdns.org/docs/validation) | Rules, FluentValidator, FormRequest |
| [Caching](https://zero-ping.duckdns.org/docs/caching) | File, array, and database cache |
| [Queues](https://zero-ping.duckdns.org/docs/queues) | Background job processing |
| [Scheduler](https://zero-ping.duckdns.org/docs/scheduler) | Task scheduling |
| [Security](https://zero-ping.duckdns.org/docs/security) | Encryption, hashing, CSRF |
| [Testing](https://zero-ping.duckdns.org/docs/testing) | Writing and running tests |
| [Best Practices](https://zero-ping.duckdns.org/docs/best-practices) | Recommended patterns and conventions |
| [Deployment](https://zero-ping.duckdns.org/docs/deployment) | Deploying ZeroPing to production |

---

## Ecosystem

ZeroPing is more than a framework — it's a growing ecosystem of tools and services.

| Product | Description | Status |
|---------|-------------|--------|
| **[Framework](https://github.com/rith-1437/ZeroPing)** | The core PHP framework | Stable |
| **[CLI](https://zero-ping.duckdns.org/docs/cli)** | `php zero` developer toolchain | Stable |
| **[Arena](https://zero-ping.duckdns.org/arena)** | Benchmark dashboard, framework comparisons, performance charts, interactive playground | Stable |
| **[Packages](https://zero-ping.duckdns.org/packages)** | First-party and community packages | Stable |
| **[Docs](https://zero-ping.duckdns.org)** | Full documentation website | Stable |
| **ZeroPing Deploy** | One-command deployment tooling | Preview |
| **ZeroPing Studio** | Visual project manager and GUI | Preview |
| **ZeroPing Cloud** | Managed hosting for ZeroPing apps | Coming Soon |
| **ZeroPing Forge** | Server provisioning and management | Coming Soon |
| **[Showcase](https://zero-ping.duckdns.org/showcase)** | Apps built with ZeroPing | Growing |

---

## Contributing

Contributions are welcome and appreciated. Here's how to get started:

1. **Fork** the repository and clone it locally.
2. **Install** dependencies: `composer install`
3. **Run the test suite** to confirm everything passes:
   ```bash
   composer test
   ```
4. **Run static analysis**:
   ```bash
   composer static-analysis
   ```
5. **Make your changes**, add tests where relevant, and open a pull request.

Please read the [contribution guide](CONTRIBUTING.md) before submitting. All contributors are expected to follow the [Code of Conduct](CODE_OF_CONDUCT.md).

- [Open an issue](https://github.com/rith-1437/ZeroPing/issues)
- [Start a discussion](https://github.com/rith-1437/ZeroPing/discussions)
- [Sponsor the project](https://github.com/sponsors/rith-1437)

---

## Roadmap

Planned features and improvements for upcoming releases:

- **GraphQL support** — first-party query layer on top of the existing ORM
- **WebSocket support** — real-time events without external dependencies
- **Rate limiter improvements** — sliding window algorithm and Redis driver
- **Improved CLI scaffolding** — interactive prompts for all `make:*` commands
- **ZeroPing Cloud** — managed hosting with zero-config deployment
- **ZeroPing Forge** — automated server provisioning and management
- **Expanded test coverage** — target 90%+ coverage across all core modules

See the [GitHub Issues](https://github.com/rith-1437/ZeroPing/issues) and [Discussions](https://github.com/rith-1437/ZeroPing/discussions) for the full roadmap and to suggest features.

---

## License

ZeroPing is open-source software licensed under the [MIT license](LICENSE).
