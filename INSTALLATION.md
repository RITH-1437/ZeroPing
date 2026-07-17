# ZeroPing — Installation & Getting Started

A complete guide to installing ZeroPing and shipping your first application in
minutes.

- [Requirements](#requirements)
- [Quick Start](#quick-start)
- [Installation](#installation)
- [The Installation Wizard](#the-installation-wizard)
- [Environment Doctor](#environment-doctor)
- [Starter Templates](#starter-templates)
- [Project Structure](#project-structure)
- [CLI Reference](#cli-reference)
- [Common Errors](#common-errors)
- [Troubleshooting](#troubleshooting)
- [Deployment](#deployment)
- [Examples](#examples)

---

## Requirements

| Requirement | Minimum |
| ----------- | ------- |
| PHP         | 8.1+    |
| Composer    | 2.0+    |

**Required PHP extensions:** `pdo`, `mbstring`, `json`, `ctype`, `tokenizer`,
`fileinfo`, `openssl`, `hash`.

**Database driver (optional, but needed for a database):** `pdo_mysql` or
`pdo_sqlite`.

Verify your machine at any time:

```bash
php zero doctor
```

---

## Quick Start

### Method 1 — Zero CLI (Recommended)

The fastest way to create a new ZeroPing application:

```bash
php zero new my-app
cd my-app
php zero serve
```

Then open <http://localhost:1437>.

### Method 2 — Composer

If Composer is already installed on your system:

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

> **Package name:** `rith-1437/zeroping` — all lowercase, no hyphen
> (the GitHub repo is `RITH-1437/ZeroPing`).

Both methods generate the exact same project structure. Use **Zero CLI** for the
fastest experience, or **Composer** if it's already installed on your system.

That's it — the installer automatically creates your `.env`, generates an
`APP_KEY`, prepares the writable directories, and verifies your environment. No
manual steps are required unless you intentionally defer database configuration.

---

## Installation

### Option A — Zero CLI (Recommended)

```bash
php zero new my-app
cd my-app
php zero serve
```

Creates a new ZeroPing application instantly using the Zero CLI.

### Option B — Composer

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

The `post-create-project-cmd` installer runs automatically and will:

1. Verify your PHP version, extensions, and Composer version.
2. Create the writable runtime directories (`storage/*`, `bootstrap/cache`).
3. Copy `.env.example` to `.env`.
4. Generate a secure `APP_KEY`.
5. Print a welcome message with your next steps.

### Option C — From source (contributors)

```bash
git clone https://github.com/RITH-1437/ZeroPing.git
cd ZeroPing
composer install
cp .env.example .env
php zero key:generate
```

### Option C — Docker

A Docker Compose setup ships with the framework:

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app cp -n .env.example .env
docker compose exec app php zero key:generate
docker compose exec app php zero migrate
```

---

## The Installation Wizard

Prefer a guided, interactive setup? Run:

```bash
php zero install
```

The wizard walks you through:

```
Welcome → Environment → Database → Application Name
        → Generate APP_KEY → Run migrations → Starter template → Success
```

Every prompt has a sensible `[default]` — just press Enter to accept it. Invalid
input is re-prompted, and any failure (for example an unavailable database) is
reported with a friendly, actionable message instead of a stack trace.

---

## Environment Doctor

`php zero doctor` runs a full diagnostic and reports each check as
**PASS**, **WARN**, or **FAIL**:

```
Runtime
  PASS  PHP version — PHP 8.3.0
  PASS  PHP extensions — pdo, mbstring, json, ...
  PASS  Composer — v2.7.0

Application
  PASS  Environment file — .env present
  PASS  Application key — set
  PASS  Configuration — 13 file(s) loaded

Filesystem
  PASS  Runtime directories — writable
  PASS  Storage — writable
  PASS  Cache — writable

Services
  WARN  Database connection — unavailable
```

A non-zero exit code is returned if any **FAIL** is present, which makes it
useful in CI pipelines.

---

## Starter Templates

Scaffold a ready-to-run project from a template:

```bash
php zero new <template> --name="My App" [--dir=path]
```

| Template    | Description                                             |
| ----------- | ------------------------------------------------------- |
| `empty`     | Minimal skeleton with a polished welcome page.          |
| `mvc`       | Full MVC CRUD scaffold with user management.            |
| `blog`      | Blog with posts, categories, and pagination.            |
| `api`       | RESTful API with a JSON root, health check, and auth.   |

Each template boots out of the box and serves a working homepage.

---

## Project Structure

```
my-app/
├── app/                # Your application code
│   ├── Controllers/    # HTTP controllers
│   ├── Models/         # ORM models
│   ├── Providers/      # Service providers
│   └── Core/           # The ZeroPing framework (self-contained)
├── bootstrap/          # Bootstrap + compiled caches
├── config/             # Configuration files
├── database/           # Migrations and seeders
├── public/             # Web root (index.php, assets)
├── storage/            # Logs, cache, and generated files
├── views/              # Templates and layouts
├── .env                # Your environment (never committed)
├── .env.example        # Environment template
└── zero                # The CLI entry point
```

---

## CLI Reference

Run `php zero help` for the full, grouped listing. The most common commands:

| Command                       | Description                                  |
| ----------------------------- | -------------------------------------------- |
| `php zero serve [port]`       | Start the development server (default 1437). |
| `php zero install`            | Interactive installation wizard.             |
| `php zero doctor`             | Verify the installation and environment.     |
| `php zero key:generate`       | Generate the application key.                |
| `php zero new <template>`     | Scaffold a project from a starter template.  |
| `php zero migrate`            | Run database migrations.                     |
| `php zero migrate:fresh`      | Drop all tables and re-run migrations.       |
| `php zero make:controller X`  | Generate a controller.                       |
| `php zero make:model X`       | Generate a model.                            |
| `php zero route:list`         | List all registered routes.                  |
| `php zero optimize`           | Cache config, routes, and views.             |
| `php zero test`               | Run the test suite.                          |

---

## Common Errors

| Symptom                                   | Cause & Fix                                                                 |
| ----------------------------------------- | -------------------------------------------------------------------------- |
| `Could not find package rith-1437/...`    | Use the exact name `rith-1437/zeroping` and ensure Composer 2.x.          |
| `Missing PHP extensions: ...`             | Enable the listed extensions in your `php.ini`, then re-run `php zero doctor`. |
| `Directory is not writable`               | Grant write permission to `storage/` and `bootstrap/cache/`.               |
| `The .env file is missing`                | Copy `.env.example` to `.env`, then run `php zero key:generate`.           |
| `Database connection unavailable`         | Update the `DB_*` values in `.env` and make sure the server is running.    |
| `Target directory already exists`         | Choose a different project name or remove the existing folder.             |

The installers never print raw stack traces — every failure includes a clear
next step.

---

## Troubleshooting

- **Nothing renders / blank page.** Set `APP_DEBUG=true` in `.env` and reload to
  see the error, then run `php zero doctor`.
- **Port already in use.** Serve on another port: `php zero serve 9000`.
- **Config changes not applied.** Clear the compiled caches:
  `php zero optimize:clear`.
- **Permission denied on Linux/macOS.** `chmod -R ug+w storage bootstrap/cache`.
- **Class not found after adding files.** Run `composer dump-autoload`.

---

## Deployment

1. Install optimized dependencies:
   ```bash
   composer install --no-dev --optimize-autoloader
   ```
2. Set production values in `.env`:
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   ```
3. Ensure an `APP_KEY` is set: `php zero key:generate`.
4. Cache configuration, routes, and views:
   ```bash
   php zero optimize
   ```
5. Point your web server's document root at the `public/` directory.
6. Make `storage/` and `bootstrap/cache/` writable by the web server user.
7. Run migrations: `php zero migrate`.
8. Confirm everything is healthy: `php zero doctor`.

---

## Examples

**Create a REST API and hit its root:**

```bash
php zero new shop-api
cd shop-api
php zero new api --name="Shop API" --dir=.   # optional overlay
php zero serve
# GET http://localhost:1437/  -> {"framework":"ZeroPing","status":"ok",...}
```

Or with Composer:

```bash
composer create-project rith-1437/zeroping shop-api
cd shop-api
php zero serve
```

**Generate a controller and a model:**

```bash
php zero make:controller PostController
php zero make:model Post
php zero make:migration create_posts_table
php zero migrate
```

**Verify before shipping:**

```bash
php zero optimize
php zero doctor
```

For more, see the [documentation](https://github.com/RITH-1437/ZeroPing/tree/main/docs).
