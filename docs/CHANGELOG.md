# Changelog

## v2.0.0-beta (2026-07-14)

Beta of the **Enterprise Framework Foundation** (Phases G+H) — the framework is now
production-shaped: a zero-dependency, fully-tested PHP core with 20 integrated
subsystems. This release hardens the foundation ahead of the stable `2.0.0` cut.

### Features
- **Dependency Injection** container with automatic resolution and **Service Providers**.
- **HTTP Kernel**, **API Resources** (JSON resources + collections) and a full **Response system**.
- **Validation**, **Localization** (translator + `lang/` files + helpers), **Cache**, **Session** and **File Storage** drivers.
- **Testing harness** compatible with PHPUnit: HTTP client, database assertions and fluent `TestResponse` assertions.
- **Benchmark** and **Profiler** for performance measurement.
- **Debug Toolbar** wiring to the framework's collectors.
- **Security** (hashing, encryption, secure random), **Logging** and **Error Handling** (exception handler + pretty pages).
- **Markdown documentation subsystem** (`app/Core/Docs`) serving the `/docs/{page}` route from `resources/docs`.
- **Router** error pages now emit the correct HTTP status code (404/500) instead of always `200`.

### Bug Fixes
- **ORM soft deletes are now opt-in** — `QueryBuilder` no longer appended `deleted_at IS NULL` to every query.
- **`Router::renderError()`** calls `http_response_code($code)` so error responses carry the right status.
- **`Docs::normalize()`** preserves dots in slugs (e.g. `release-2.0.0-beta`) so versioned doc pages resolve.

### Testing & Quality
- Full suite green: **375 tests, 901 assertions, 0 failures**.
- `phpcs` (PSR-12): **0 errors, 0 warnings** across `app/`, `config/`, `tests/`, `scripts/` and `public/`
  (generated cache, CLI command tables and test fixtures are carve-outs).

## v1.3.0 (2026-07-14)

This release is all about **developer experience** — a polished console, a richer
`about` screen, clearer error pages, and a `publish` command so projects can
override framework assets without editing vendor code.

### ✨ Features

- **Console branding** — a gradient ASCII `zero` logo and tagline now greet you on
  `php zero`, `php zero about`, and at the end of the interactive installer.
- **Grouped, colorized command table** — `php zero` lists commands organized into
  logical groups (Project, Database & Migrations, Generators, Routes, Config &
  Cache, Queue & Schedule, Storage & Search, Security & Keys, Testing &
  Diagnostics, Utilities) instead of one flat list.
- **Rich `php zero about`** — now reports framework version, PHP version, Composer
  version, environment, database driver, application name, timezone, and the
  cache/session/queue drivers, plus quick links to the docs and GitHub.
- **Better error pages** — added dedicated `403` (Forbidden) and `419` (Page
  Expired / CSRF) screens. The development `404` and `500` pages now show request
  URL/method, environment, and a full stack trace; production shows a clean,
  minimal page. All error views support both `APP_DEBUG` modes.
- **`php zero publish`** — copies framework-supplied assets (config files, error
  views, language files, `public/robots.txt`) into your project so they can be
  customized. Use `--group=config|views|lang|public|all`; existing files are
  never overwritten.
- **Per-command help** — every command supports `php zero <command> --help`
  (including `php zero help <command>`), showing usage, arguments, options,
  examples, and notes.
- **Environment validation on install** — `php zero install` now prompts for a
  timezone and validates the generated `.env` (required keys present, valid
  timezone, sane `APP_URL`/debug flags) before finishing.
- **Improved `php zero route:list`** — adds a `Name` column for named routes and
  color-codes HTTP methods (GET green, POST blue, PUT/PATCH yellow, DELETE red).

### 📚 Documentation

- README now highlights the v1.3.0 developer-experience improvements.
- CLI Reference documents the new `publish` command and per-command `--help`.
- Roadmap updated: the friendlier development error screen (previously near-term)
  shipped in v1.3.0.

### 🧪 Testing

- Full suite remains green (274 tests, 603 assertions) after the v1.3.0 changes.

## v1.2.0 (2026-07-12)

This release focuses on making ZeroPing a polished, publicly installable PHP
framework — Composer-ready packaging, richer developer tooling, more starter
templates, and documentation accuracy.

### ✨ Features

- **Public distribution** — `composer.json` is now a `project` type with declared
  PHP extension requirements, a `zero` binary, and a `post-create-project-cmd`
  installer (`scripts/post-create-project.php`) that provisions storage, copies
  `.env`, and generates `APP_KEY`.
- **`php zero doctor`** — verifies PHP version, required extensions, `.env`
  presence, writable runtime directories, the application key, and database
  connectivity.
- **`php zero about`** — prints framework, PHP, and environment information.
- **`php zero make:test`** — scaffolds unit/feature tests (`--feature` flag).
- **`php zero make:command`** — scaffolds new console commands.
- **`php zero serve <port>`** — the development server now accepts an optional
  port argument (defaults to `1437`).
- **Starter template: `dashboard`** — admin dashboard with summary widgets and
  user management, available via `php zero new dashboard`.
- **`.gitattributes`** — keeps dev, test, and docs assets out of the Packagist
  distribution archive.

### 📚 Documentation

- README now promotes `composer create-project rith-1437/zero-ping` and links to
  the correct GitHub repository and Packagist package.
- `docs/website/installation.md` rewritten to reflect the `create-project` flow,
  the `php zero` CLI, and accurate commands.

### 🧪 Testing

- Added `StarterTemplatesTest` (guards template directories) and
  `ConsoleCommandsTest` (every command class is instantiable).

## v1.1.0 (2026-07-09)

### ✨ Features

- **Documentation Search** — Full-text search across all documentation pages with fuzzy matching (Levenshtein distance), AJAX endpoint (`/search?q=`), debounced frontend integration, recent searches in `localStorage`, highlighted results, and empty/error states.
- **Starter Templates** — `php zero new {type}` scaffolds a complete project from one of four templates: `empty` (minimal skeleton), `blog` (posts with pagination), `api` (RESTful JSON endpoints), `mvc` (full CRUD with user management). Supports `--name` and `--dir` options.
- **Enhanced Validation** — 8 new validation rules: `array`, `file`, `image`, `mimes`, `size`, `in`, `not_in`, `regex`. New `FluentValidator` chainable API. New `FormRequest` base class with `validated()` method, custom error messages, and `ValidationException`.
- **Performance Optimizations** — Real view caching (compiled to `storage/cache/views/`), route caching with serialized route files, configuration caching, and lazy service loading. Enhanced `optimize` command now also builds search index. `optimize:clear` also clears search index cache.
- **CLI Enhancements** — Added `new` and `search:index` commands. Updated help text. Consistent output formatting across all commands.

### 🐛 Bug Fixes

- Fixed `BASE_PATH` not being defined in web request context (was only set via CLI bootstrap path)
- Fixed 5 service providers (`Cache`, `Filesystem`, `Mail`, `Queue`, `Schedule`) that passed `$app` to constructors expecting no arguments
- Fixed `Token.php` — added missing `User` import and replaced non-existent container binding with direct DI
- Fixed `DatabaseTokenRepository.php` — added missing `User` import and replaced broken QueryBuilder calls with raw PDO
- Fixed `ORM\Collection.php` — created missing `Arrayable`/`Jsonable` contracts that were referenced in `instanceof` checks
- Fixed hardcoded database password in `.env`
- Removed `public/test.php` which bypassed routing (security issue)
- Fixed `PrettyException.php` — escaped exception message output to prevent XSS
- Fixed `CommandEvent::run()` — wrapped shell command with `escapeshellcmd()` to prevent command injection
- Fixed `Random::uuid()` — replaced `mt_rand()` with `random_bytes()` for cryptographically secure UUIDs
- Fixed `CSRFToken` — supports multiple valid tokens so multi-tab browsing works; added `regenerate()` method
- Fixed `View::render()` — replaced `die()` calls with `\RuntimeException` for proper error handling
- Fixed `View::render()` — now both echoes output (for `Controller::view()`) and returns string (for `view()` helper)

### ♻️ Refactoring

- `PasswordHasher` now extends `Hash` (eliminated duplicate bcrypt logic)
- `Session` now extends `SessionGuard` (eliminated duplicate session logic)
- `Support\Validator` now delegates to `Validation\Validator` (unified validation pipeline)
- Created `App\Core\Contracts\Arrayable` and `App\Core\Contracts\Jsonable` interfaces
- Added `base_path()` helper function
- Added `Random::string()` method for token generation
- Removed dead view files: `views/layouts/app.php`, `views/emails/test_mail.php`
- Added PHPDoc to `ConsoleStyle`, `Console`, and `Random` classes

### 📚 Documentation

- New documentation pages: Search, Starter Templates, Validation, CLI Reference, Performance
- All new pages registered in `DocsService` sidebar
- Roadmap updated to reflect completed v1.1 features

### 🧪 Testing

- New tests: `ValidationRulesTest`, `FluentValidatorTest`, `FormRequestTest`, `SearchIndexTest`
- All existing tests preserved and compatible with refactored code

### 🔒 Security

- Removed hardcoded credentials from `.env`
- Command injection protection in scheduler
- XSS prevention in exception handler
- Cryptographically secure random UUIDs
- Multi-tab compatible CSRF tokens
- Removed debug bypass endpoint (`public/test.php`)

### ⚠️ Breaking Changes

- `View::render()` now returns `string` instead of `void` (previously discarded output). Callers using `$this->view()` in controllers are unaffected. Direct calls to `View::render()` should handle the return value or use `echo`.
- `CSRFToken::get()` no longer regenerates the token on every call. Use `CSRFToken::regenerate()` for explicit rotation.
- `PasswordHasher` now extends `Hash`. Both `make()` and `check()` remain available with identical signatures.
- `Session` now extends `SessionGuard`. All public methods remain available.
- `Support\Validator` now delegates to `Validation\Validator`. The public API (`__construct`, `validate`, `passes`, `errors`) is unchanged.
