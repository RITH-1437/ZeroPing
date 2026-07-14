# ZeroPing Framework

<p align="center">
  <a href="https://github.com/rith-1437/zero-ping">
    <img src="https://raw.githubusercontent.com/rith-1437/zero-ping/main/public/assets/images/logo.png" alt="ZeroPing Logo" width="200">
  </a>
</p>

<p align="center">
  <a href="https://packagist.org/packages/rith-1437/zero-ping"><img src="https://img.shields.io/packagist/v/rith-1437/zero-ping.svg?style=flat-square" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/rith-1437/zero-ping"><img src="https://img.shields.io/packagist/dt/rith-1437/zero-ping.svg?style=flat-square" alt="Total Downloads"></a>
  <a href="https://github.com/rith-1437/zero-ping/actions"><img src="https://img.shields.io/github/actions/workflow/status/rith-1437/zero-ping/ci.yml?style=flat-square" alt="Build Status"></a>
  <a href="https://github.com/rith-1437/zero-ping/blob/main/LICENSE"><img src="https://img.shields.io/github/license/rith-1437/zero-ping?style=flat-square" alt="License"></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square" alt="PHP >= 8.1">
  <a href="https://github.com/devcontainers/features"><img src="https://img.shields.io/badge/Dev%20Container-ready-007ACC.svg?style=flat-square" alt="Dev Container Ready"></a>
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

## What's New in v1.3.0

The v1.3.0 release focuses on **developer experience**:

- **Polished console** — a gradient `zero` logo and a grouped, colorized command
  table. Run `php zero` to see it.
- **Richer `php zero about`** — framework version, PHP/Composer versions,
  database driver, timezone, and your cache/session/queue drivers at a glance.
- **Per-command help** — every command supports `--help` (or `php zero help <command>`),
  showing usage, arguments, options, and examples.
- **Better error pages** — dedicated `403`/`419` screens and development `404`/`500`
  pages with request details and a full stack trace; production shows clean pages.
- **`php zero publish`** — copy framework config, views, language files, and
  `public/robots.txt` into your project so you can customize them safely
  (`--group=config|views|lang|public|all`).
- **Improved `php zero route:list`** — named routes shown plus color-coded methods.
- **Install-time validation** — `php zero install` prompts for a timezone and
  validates your `.env` before finishing.

## Requirements

- PHP >= 8.1
- Composer
- MySQL / MariaDB (or another PDO-supported database)

## Installation

### Option A — Composer (recommended)

Start a new project with Composer (downloads the packaged release from Packagist):

```bash
composer create-project rith-1437/zero-ping my-app
cd my-app

# The installer copies .env, generates your APP_KEY, and prepares storage.
php zero serve
```

> **Package name:** `rith-1437/zero-ping` — all lowercase, with a hyphen
> (the GitHub repo is `rith-1437/zero-ping`).

The `post-create-project-cmd` script runs automatically after install. Prefer a
guided setup? Run `php zero install` for an interactive wizard, or
`php zero doctor` to verify your environment at any time.

### Option B — Docker (development environment)

A Docker Compose setup is included for local development with PHP 8.3, Nginx, and MySQL:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app cp -n .env.example .env
docker compose exec app php zero key:generate
docker compose exec app php zero migrate
```

The app will be available at `http://localhost`.

For VS Code users, a [devcontainer](.devcontainer/devcontainer.json) configuration is also provided — reopen the project in the container for a zero-setup development environment.

### Option C — Clone from GitHub

To work from the source repository instead:

```bash
git clone https://github.com/rith-1437/zero-ping.git my-app
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
queues, scheduling, security, testing, deployment, extending the framework, and the CLI reference.

A quick tour of the most common commands:

```bash
php zero serve          # start the development server
php zero route:list     # list all registered routes
php zero make:controller BlogController
php zero make:model Post
php zero migrate        # run pending migrations
php zero test           # run the test suite
php zero about          # framework, PHP, and environment info
php zero --help         # list all available commands (grouped & colorized)
```

## Quick Start

A new ZeroPing project ships with a small demo website so you can see it running
immediately. To add your own page alongside it, follow these three steps.

**1. Register a route** (add this to the end of `config/routes.php`):

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
php zero make:controller   # scaffold a controller (--resource for CRUD)
php zero make:model        # scaffold a model (--all for the full stack)
php zero make:job          # scaffold a queue job
php zero make:event        # scaffold an event
php zero make:listener     # scaffold an event listener (--event=)
php zero make:notification # scaffold a notification
php zero make:factory      # scaffold a model factory (--model=)
php zero make:enum         # scaffold a string-backed enum
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
  [GitHub Discussions](https://github.com/rith-1437/zero-ping/discussions).
- 🐞 **Issues**: report bugs and request features using the
  [issue templates](https://github.com/rith-1437/zero-ping/issues/new/choose).
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
