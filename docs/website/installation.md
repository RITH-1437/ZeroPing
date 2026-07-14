# Installation

ZeroPing is distributed as a Composer package. The recommended way to start a
new project is `composer create-project`, which installs the framework, copies
your environment file, generates an application key, prepares storage, and
creates the default SQLite database — all with **zero manual configuration**.

## Requirements

- **PHP 8.1 or higher** (tested through 8.5).
- The following PHP extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`,
  `ctype`, `tokenizer`, `fileinfo`, `openssl`, `hash`.
- **Composer 2.x**.
- A database. **SQLite is the default and needs nothing installed** — a single
  file (or `:memory:`) is enough. For production you can switch to
  MySQL, MariaDB, or PostgreSQL (see [Database](#database)).

> Windows, Linux, and macOS are all supported. Paths are resolved with the
> platform directory separator, so the same commands work everywhere.

## Quick Start

```bash
composer create-project rith-1437/zero-ping my-app
cd my-app
php zero serve
```

Then open <http://localhost:1437>. That is the entire setup for a working
application — no server to provision, no credentials to type.

> **Package name:** `rith-1437/zero-ping` — all lowercase, hyphenated
> (the GitHub repository is `RITH-1437/ZeroPing`).

## What happens automatically

After `composer create-project`, the `post-create-project-cmd` script runs
and, without prompting, will:

1. Verify the PHP version and required extensions.
2. Create the runtime directories (`storage/cache`, `storage/logs`,
   `bootstrap/cache`, …) with writable permissions.
3. Copy `.env.example` to `.env` (you never copy it by hand).
4. Generate a random `APP_KEY`.
5. Create `database/database.sqlite` (the default database).
6. Optimise the Composer autoloader.
7. Print a friendly summary with the next steps.

If anything is missing (for example a required extension), the script reports a
clean, actionable message instead of a stack trace.

## Interactive setup wizard

Prefer to be guided? Run the wizard after (or instead of) the automatic
step:

```bash
php zero install
```

It asks for your application name, environment, timezone, and database driver
(**SQLite** recommended, **MySQL**, **MariaDB**, or **PostgreSQL**), validates
the database connection before continuing, generates `APP_KEY`, and offers to
run migrations. Before finishing, the wizard **validates your generated `.env`** —
confirming required keys are present, the timezone is valid (it falls back to
`UTC` if not), and `APP_URL`/`APP_DEBUG` are well-formed — so you start from a
known-good configuration. A completion screen shows the next steps.

## Alternative: install from source

If you are contributing to the framework itself, clone the repository:

```bash
git clone https://github.com/rith-1437/zero-ping.git
cd ZeroPing
composer install
cp .env.example .env
php zero key:generate
```

## Configuration

Configuration lives in `config/*.php` and is read from `.env` (environment
variables). Keep secrets in `.env`; the config files only reference them via
`env()`.

The most important keys:

| Key | Purpose | Default |
|-----|---------|---------|
| `APP_NAME` | Human-readable application name | `ZeroPing` |
| `APP_ENV` | `local`, `development`, or `production` | `production` |
| `APP_DEBUG` | Show detailed errors | `false` |
| `APP_KEY` | Encryption key (auto-generated) | — |
| `APP_URL` | Base URL | `http://localhost:1437` |
| `APP_TIMEZONE` | Default PHP timezone | `UTC` |
| `DB_CONNECTION` | `sqlite`, `mysql`, `mariadb`, `pgsql` | `sqlite` |

## Database

ZeroPing ships with four drivers. **New projects default to SQLite**, so a
fresh install works with no database server. Switching engines is a pure
configuration change — no model, migration, or query code has to change.

### SQLite (default)

```dotenv
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

That is the entire configuration. With no `DB_DATABASE` set, ZeroPing falls
back to `database/database.sqlite`. Use `DB_DATABASE=:memory:` for tests.

### MySQL / MariaDB

```dotenv
DB_CONNECTION=mysql          # or: mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zero_ping
DB_USERNAME=root
DB_PASSWORD=secret
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

MariaDB uses the identical block with `DB_CONNECTION=mariadb`.

### PostgreSQL

```dotenv
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=zero_ping
DB_USERNAME=postgres
DB_PASSWORD=secret
DB_CHARSET=utf8
DB_SCHEMA=public
```

See [Database](../database.md) for the full reference, including how to run
migrations against each engine.

## Starter Templates

Kick-start a project structure with a starter template:

```bash
php zero new blog --name="My Blog"
```

Available templates: `empty` (minimal skeleton), `mvc`, `blog`, `api`, and
`dashboard`. Each scaffolds `app/`, `config/`, and `views/` so you start from
a sensible baseline. Use `--dir=` to scaffold into a specific folder.

## CLI

ZeroPing ships a batteries-included CLI (`php zero`). The commands most
relevant to installation and onboarding:

| Command | Description |
|---------|-------------|
| `php zero serve` | Start the development server (default port 1437). |
| `php zero install` | Interactive setup wizard. |
| `php zero doctor` | Verify PHP, extensions, environment, and database. |
| `php zero migrate` | Run database migrations. |
| `php zero key:generate` | Generate `APP_KEY`. |
| `php zero new <name>` | Scaffold a new app from a starter template. |
| `php zero make:controller` | Scaffold a controller. |
| `php zero make:model` | Scaffold a model. |

Run `php zero` with no arguments to list every available command.

## Verify your installation

Run the built-in doctor to confirm PHP, extensions, environment, and database
connectivity are correctly configured:

```bash
php zero doctor
```

It prints `PASS`, `WARNING`, and `ERROR` lines with colored output for each
check: PHP version, Composer, PDO, the SQLite/MySQL/PostgreSQL extensions,
required extensions, storage, cache, configuration, environment, and
`APP_KEY`.

## Troubleshooting

**`php zero doctor` reports "PDO extension not loaded".**
Enable `pdo` (and the driver you use: `pdo_sqlite`, `pdo_mysql`, or
`pdo_pgsql`) in your `php.ini`, then re-run `php zero doctor`.

**"Could not open the SQLite database" / directory not writable.**
Make sure the project directory is writable, or create the database file
manually: `touch database/database.sqlite`.

**The welcome page shows a 404.**
You are likely missing `config/routes.php`. Re-run
`composer create-project` (or copy `config/routes.php` from a fresh install).
The default route points at the ZeroPing welcome controller.

**"Controller App\Controllers\HomeController not found".**
You scaffolded with a starter template but have not regenerated the autoloader.
Run `composer dump-autoload`.

**Migrations fail with a connection error.**
Check `DB_CONNECTION` and the related `DB_*` values in `.env`. For MySQL or
PostgreSQL, confirm the server is reachable and the credentials are correct.
`php zero doctor` will point at the exact problem.

## FAQ

**Do I need a database server to start?**
No. SQLite is the default and stores everything in a single file. You only
need MySQL, MariaDB, or PostgreSQL when you choose them.

**Is the welcome page the real application?**
The welcome page is a built-in landing screen shown on a fresh install. Edit
`config/routes.php` and add controllers under `app/Controllers` to build your
app.

**Can I change the database later?**
Yes — change `DB_CONNECTION` (and the related `DB_*` values) in `.env`. No
code changes are required; the migrations table is created for the new engine
automatically.

**Where do I put my environment secrets?**
In `.env` (already git-ignored). `config/*.php` reads them via `env()` and
should never contain real credentials.

## Examples

A minimal end-to-end flow:

```bash
# 1. Create the project (SQLite, zero config)
composer create-project rith-1437/zero-ping blog
cd blog

# 2. Confirm the environment
php zero doctor

# 3. Run migrations
php zero migrate

# 4. Start developing
php zero serve
```

Switching to MySQL for production:

```dotenv
# .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blog
DB_USERNAME=blog_user
DB_PASSWORD=strong-password
```

```bash
php zero migrate:fresh   # recreate the schema on the new engine
```
