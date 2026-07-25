# demo

A lightweight application with only the essentials.

> Built with the [ZeroPing](https://github.com/RITH-1437/ZeroPing) framework (v2.0.1).

## Introduction

demo is a ZeroPing application generated with the **Empty Starter** starter.
ZeroPing is a lightweight, modern PHP framework with a clean MVC architecture,
a multi-driver ORM, validation, caching, queues and batteries-included CLI
tooling.

## Requirements

- PHP >= 8.5+
- Composer
- A PDO database driver (SQLite is bundled and used by default)

## Installation

```bash
composer install
cp .env.example .env
php zero key:generate
```

## Running

```bash
php zero serve
```

Then open [http://127.0.0.1:1437](http://127.0.0.1:1437) in your browser.

## Useful Commands

```bash
php zero serve          # start the development server
php zero migrate         # run database migrations
php zero make:controller # scaffold a controller
php zero make:model      # scaffold a model
php zero test            # run the test suite
php zero doctor          # verify your environment
```

## Folder Structure

```
app/            Application code (Controllers, Models, Middleware, ...)
config/         Configuration files
database/       Migrations and seeders
routes/         Route definitions
views/          View templates
public/         Public web root (entry point)
storage/        Runtime caches, logs and framework files
tests/          PHPUnit tests
```

## Documentation

- [Introduction](https://zero-ping.duckdns.org/docs/introduction)
- [Installation](https://zero-ping.duckdns.org/installation)
- [Features](https://zero-ping.duckdns.org/features)
- [API Reference](https://zero-ping.duckdns.org/api)

## Community

- [GitHub Discussions](https://github.com/RITH-1437/ZeroPing/discussions)
- [Issue Tracker](https://github.com/RITH-1437/ZeroPing/issues)

## GitHub

Source and contributions: [github.com/RITH-1437/ZeroPing](https://github.com/RITH-1437/ZeroPing)

---

*Generated with ZeroPing Framework v2.0.1 — 2026*