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

```bash
# Clone the repository
git clone https://github.com/RITH-1437/ZeroPing.git
cd ZeroPing

# Install dependencies
composer install

# Copy the environment file and configure
cp .env.example .env

# Run migrations and start the development server
php zero migrate
php zero serve
```

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
