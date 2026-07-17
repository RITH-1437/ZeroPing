# Installation

ZeroPing can be installed in two ways: the **Zero CLI** (recommended for the fastest experience) or **Composer** (if it's already on your system). Both methods generate the exact same project structure.

## Requirements

- **PHP 8.1 or higher** (tested through 8.5).
- **Composer 2.x** (for the Composer method, or for dependency management after scaffolding).
- **MySQL**, **MariaDB**, **PostgreSQL**, or **SQLite** (default, zero-config).

> Windows, Linux, and macOS are all supported. Paths are resolved with the platform directory separator, so the same commands work everywhere.

## Quick Start

### Method 1 — Zero CLI (Recommended)

```bash
php zero new my-app
cd my-app
php zero serve
```

Then open [http://localhost:1437](http://localhost:1437).

### Method 2 — Composer

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

> **Package name:** `rith-1437/zeroping`
> (the GitHub repository is `RITH-1437/ZeroPing`).

Both methods generate the exact same project structure. Use **Zero CLI** for the fastest experience, or **Composer** if it's already installed on your system.

## What Happens Automatically

After installation, the setup script runs and, without prompting, will:

1. Verify the PHP version and required extensions.
2. Create runtime directories (`storage/cache`, `storage/logs`, `bootstrap/cache`).
3. Copy `.env.example` to `.env`.
4. Generate a random `APP_KEY`.
5. Create `database/database.sqlite` (the default database).
6. Optimise the Composer autoloader.
7. Print a friendly summary with the next steps.

## Interactive Setup Wizard

Prefer to be guided? Run the wizard:

```bash
php zero install
```

It asks for your application name, environment, timezone, and database driver (SQLite recommended, MySQL, MariaDB, or PostgreSQL), validates the database connection, generates `APP_KEY`, and offers to run migrations.

## Docker

A Docker Compose setup is included for local development:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app cp -n .env.example .env
docker compose exec app php zero key:generate
docker compose exec app php zero migrate
```

The app will be available at `http://localhost`.

## Verify Your Installation

```bash
php zero doctor
```

It prints `PASS`, `WARNING`, and `ERROR` lines for PHP version, extensions, environment, and database connectivity.

## Documentation

For the full installation guide, see [framework-site/docs/installation.md](framework-site/docs/installation.md) or the online documentation at [zeroping.dev/installation](https://zeroping.dev/installation).
