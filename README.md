<p align="center">
  <a href="https://zeroping.dev">
    <img src="https://raw.githubusercontent.com/rith-1437/ZeroPing/main/public/assets/images/mascot.svg" alt="ZeroPing" width="120">
  </a>
</p>

<h1 align="center">ZeroPing</h1>

<p align="center">
  <strong>Clean. Expressive. Familiar.</strong><br>
  A modern PHP framework built from scratch with zero external dependencies.
</p>

<p align="center">
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/v/rith-1437/zeroping.svg?style=flat-square&color=14B8A6" alt="Latest Stable Version"></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square" alt="PHP >= 8.1">
  <a href="https://github.com/rith-1437/ZeroPing/blob/main/LICENSE"><img src="https://img.shields.io/github/license/rith-1437/ZeroPing?style=flat-square" alt="License"></a>
  <a href="https://github.com/rith-1437/ZeroPing/actions"><img src="https://img.shields.io/github/actions/workflow/status/rith-1437/ZeroPing/ci.yml?style=flat-square" alt="Tests"></a>
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/dt/rith-1437/zeroping.svg?style=flat-square" alt="Downloads"></a>
</p>

---

## 📚 Documentation

| Guide | Description |
|-------|-------------|
| 🚀 [Installation](INSTALLATION.md) | Install ZeroPing using Zero CLI, Composer, Docker, or from source. |
| 📈 [Performance](PERFORMANCE.md) | Performance optimizations and best practices. |
| 🗺️ [Roadmap](ROADMAP.md) | Current release, upcoming milestones, and future plans. |
| 📦 [Release Process](RELEASING.md) | Release workflow and versioning strategy. |
| 📝 [Changelog](CHANGELOG.md) | Version history and release notes. |
| 🤝 [Contributing](CONTRIBUTING.md) | Contribution guidelines for developers. |
| 🛡️ [Security Policy](SECURITY.md) | Report vulnerabilities and supported versions. |
| ❤️ [Code of Conduct](CODE_OF_CONDUCT.md) | Community standards and expected behavior. |
| 💬 [Support](SUPPORT.md) | Get help via docs, discussions, and issues. |
| ⬆️ [Upgrade Guide](UPGRADE.md) | Upgrade between ZeroPing versions. |
| 📄 [License](LICENSE) | MIT License. |

## Introduction

ZeroPing is a lightweight, modern PHP framework built from scratch with a clean MVC architecture, a fast dependency-injection container, an expressive ORM, validation, caching, a background queue, a task scheduler, and developer-friendly CLI tooling.

It is free, open-source, and created by **Rin Nairith**.

ZeroPing aims to be approachable for newcomers while remaining powerful enough for production applications — with zero magic, readable source, and no hidden configuration.

## Why ZeroPing?

- **Zero external runtime dependencies** — the framework runs on PHP alone.
- **Readable source** — every component is designed to be understood by reading the code.
- **Stable APIs** — breaking changes are rare and well-documented.
- **375 tests, 900 assertions, 0 failures** — battle-tested from day one.
- **Production-ready** — security, caching, queues, scheduling, and error handling built in.

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

## Quick Start

### Installation

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

Open [http://localhost:1437](http://localhost:1437) — that's it.

### Build Your First App

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

## CLI Reference

ZeroPing ships a batteries-included CLI. The most common commands:

| Command | Description |
|---------|-------------|
| `php zero new my-app` | Create a new project |
| `php zero serve` | Start the development server |
| `php zero migrate` | Run database migrations |
| `php zero make:controller HomeController` | Scaffold a controller |
| `php zero make:model Post` | Scaffold a model |
| `php zero route:list` | List registered routes |
| `php zero doctor` | Verify your installation |
| `php zero --help` | List all commands |

## Project Structure

```
my-app/
├── app/                  # Application code
│   ├── Controllers/      # HTTP controllers
│   ├── Models/           # ORM models
│   ├── Middleware/       # HTTP middleware
│   ├── Services/         # Business logic
│   └── Providers/        # Service providers
├── config/               # Configuration files
├── public/               # Web entry point
├── views/                # View templates
├── database/
│   └── migrations/       # Database migrations
├── storage/              # Cache, logs, uploads
├── tests/                # Unit & feature tests
├── zero                  # CLI binary
└── .env                  # Environment config
```

## Documentation

Full documentation is available at [zeroping.dev](https://zeroping.dev):

| Page | Description |
|------|-------------|
| [Introduction](https://zeroping.dev/docs/introduction) | What ZeroPing is and why it exists |
| [Installation](https://zeroping.dev/installation) | Install and configure ZeroPing |
| [Getting Started](https://zeroping.dev/getting-started) | Build your first app |
| [CLI Reference](https://zeroping.dev/docs/cli) | Complete CLI command reference |
| [Routing](https://zeroping.dev/docs/introduction) | Routes, groups, middleware |
| [Database & ORM](https://zeroping.dev/docs/database) | Models, relationships, migrations |
| [Validation](https://zeroping.dev/docs/validation) | Rules, FluentValidator, FormRequest |
| [Caching](https://zeroping.dev/docs/caching) | File, array, and database cache |
| [Queues](https://zeroping.dev/docs/queues) | Background job processing |
| [Scheduler](https://zeroping.dev/docs/scheduler) | Task scheduling |
| [Security](https://zeroping.dev/docs/security) | Encryption, hashing, CSRF |
| [API Reference](https://zeroping.dev/api) | Classes, methods, namespaces |
| [Roadmap](https://zeroping.dev/roadmap) | Where ZeroPing is heading |

## Starter Templates

Scaffold a complete project from a pre-built template:

```bash
php zero new empty       # minimal skeleton
php zero new mvc         # full CRUD with user management
php zero new blog        # blog with posts and pagination
php zero new api         # RESTful API boilerplate
```

## Community

- **Discussions**: [GitHub Discussions](https://github.com/rith-1437/ZeroPing/discussions) — ask questions and share ideas
- **Issues**: [GitHub Issues](https://github.com/rith-1437/ZeroPing/issues) — report bugs and request features
- **Security**: [Security Policy](SECURITY.md) — report vulnerabilities privately

## Contributing

Thank you for considering contributing to ZeroPing! Please read the [contribution guide](CONTRIBUTING.md) before opening a pull request.

## Code of Conduct

To ensure the ZeroPing community stays welcoming to everyone, please review and abide by our [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within ZeroPing, please review our [security policy](SECURITY.md) for responsible disclosure instructions. You can report issues privately to **Rin Nairith** at [nairithrin143@gmail.com](mailto:nairithrin143@gmail.com).

## License

ZeroPing is open-source software licensed under the [MIT license](LICENSE).
