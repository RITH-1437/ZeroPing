# ZeroPing Framework

<p align="center">
  <a href="https://github.com/RITH-1437/ZeroPing">
    <img src="https://raw.githubusercontent.com/RITH-1437/ZeroPing/main/public/assets/images/logo.svg" alt="ZeroPing Logo" width="200">
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/v/rith-1437/zeroping.svg?style=flat-square" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/dt/rith-1437/zeroping.svg?style=flat-square" alt="Total Downloads"></a>
  <a href="https://github.com/RITH-1437/ZeroPing/actions"><img src="https://img.shields.io/github/actions/workflow/status/RITH-1437/ZeroPing/ci.yml?style=flat-square" alt="Build Status"></a>
  <a href="https://github.com/RITH-1437/ZeroPing/blob/main/LICENSE"><img src="https://img.shields.io/github/license/RITH-1437/ZeroPing?style=flat-square" alt="License"></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square" alt="PHP >= 8.1">
</p>

## About ZeroPing

ZeroPing is a lightweight, modern PHP framework built from scratch with a clean
model–view–controller (MVC) architecture, a fast dependency-injection container,
an expressive ORM, validation, caching, a background queue, a task scheduler,
and developer-friendly CLI tooling. It is free, open-source, and created by
**Rin Nairith**.

ZeroPing aims to be approachable for newcomers while remaining powerful enough
for production applications — with zero magic, readable source, and no hidden
configuration.

## Key Features

- **Expressive routing** with named routes, route groups, prefixes, and middleware.
- **Dependency Injection container** with automatic resolution and reflection caching.
- **Eloquent-style ORM** with relationships, accessors/mutators, and pagination.
- **Schema builder & migrations** for versioned, reversible database changes.
- **Validation** with a rich rule set and custom rule support.
- **Caching** with multiple drivers and a per-request memory layer.
- **Background queues** (sync, database, file) and scheduled commands.
- **Security** middleware: CSRF protection, encryption, rate limiting, and HTML escaping.
- **Blazing-fast CLI** (`php zero`) for scaffolding, migrations, and maintenance.
- **Zero external runtime dependencies** beyond PHP itself.

## Requirements

- PHP >= 8.1
- Composer
- MySQL / MariaDB (or another PDO-supported database)

## Installation

### Option A — Composer (recommended)

Start a new project with Composer (downloads the packaged release from Packagist):

```bash
composer create-project rith-1437/zeroping my-app
cd my-app

# The installer copies .env, generates your APP_KEY, and prepares storage.
php zero serve
```

The `post-create-project-cmd` script runs automatically after install.

### Option B — Clone from GitHub

To work from the source repository instead:

```bash
git clone https://github.com/RITH-1437/ZeroPing.git my-app
cd my-app

composer install
cp .env.example .env
php zero key:generate
php zero serve
```

Run `php zero doctor` at any time to verify your installation.

## Documentation

The full documentation lives in the [`docs/`](docs/index.html) directory and can
be opened directly in a browser, or built into a static site. It covers
installation, routing, the container, database, ORM, validation, caching,
queues, scheduling, security, testing, deployment, and the CLI reference.

A quick tour of the most common commands:

```bash
php zero serve          # start the development server
php zero route:list     # list all registered routes
php zero make:controller BlogController
php zero make:model Post
php zero migrate        # run pending migrations
php zero test           # run the test suite
php zero --help         # list all available commands
```

## Quick Start

After creating your project, build your first page in three steps.

**1. Register a route** (`config/routes.php`):

```php
use App\Core\Routing\Router;
use App\Controllers\HomeController;

Router::get('/', [HomeController::class, 'index']);
```

**2. Create a controller** (`app/Controllers/HomeController.php`):

```php
<?php

namespace App\Controllers;

use App\Core\View\Controller;

class HomeController extends Controller
{
    public function index(): string
    {
        return view('home', [
            'name' => config('app.name'),
        ]);
    }
}
```

**3. Create a view** (`views/home.php`):

```php
<h1>Hello from <?= e($name) ?>!</h1>
```

Start the server and visit your page:

```bash
php zero serve
# open http://localhost:1437
```

## Project Structure

A freshly created ZeroPing project looks like this:

```
my-app/
├── app/                  # Your application code
│   ├── Controllers/      # HTTP controllers
│   ├── Models/           # Eloquent-style ORM models
│   ├── Middleware/       # HTTP middleware
│   ├── Services/         # Business logic services
│   └── Providers/        # Service providers
├── config/               # routes.php, database.php, app.php
├── public/               # Web entry point (index.php)
├── views/                # Plain-PHP view templates
├── database/
│   └── migrations/       # Database migrations
├── storage/              # Cache, logs, and uploaded files
├── tests/                # Unit & Feature tests
├── zero                  # The CLI binary
└── .env                  # Environment configuration
```

## CLI Usage

ZeroPing includes a batteries-included CLI. The most common commands:

```bash
php zero serve              # start the development server
php zero migrate            # run database migrations
php zero make:controller   # scaffold a controller
php zero make:model        # scaffold a model
php zero route:list        # list registered routes
php zero doctor            # verify your installation
php zero --help             # full command reference
```

See the [CLI Reference](docs/website/cli.md) for the complete list.

## Examples

ZeroPing ships with ready-made starter templates you can scaffold instantly:

```bash
php zero new empty       # minimal skeleton
php zero new mvc         # full CRUD with user management
php zero new blog        # blog with posts and pagination
php zero new api         # RESTful API boilerplate
php zero new dashboard   # admin dashboard with widgets
```

A plan for additional example applications is available in
[docs/EXAMPLES.md](docs/EXAMPLES.md).

## Community

- 💬 **Discussions**: ask questions and share ideas at
  [GitHub Discussions](https://github.com/RITH-1437/ZeroPing/discussions).
- 🐞 **Issues**: report bugs and request features using the
  [issue templates](https://github.com/RITH-1437/ZeroPing/issues/new/choose).
- 🔒 **Security**: report vulnerabilities privately via our
  [security policy](SECURITY.md).

## Contributing

Thank you for considering contributing to ZeroPing! Please read the
[contribution guide](CONTRIBUTING.md) before opening a pull request.

## Code of Conduct

To ensure the ZeroPing community stays welcoming to everyone, please review and
abide by our [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within ZeroPing, please review our
[security policy](SECURITY.md) for responsible disclosure instructions. You can
report issues privately to **Rin Nairith** at
[nairithrin143@gmail.com](mailto:nairithrin143@gmail.com).

## License

ZeroPing is open-source software licensed under the [MIT license](LICENSE).
