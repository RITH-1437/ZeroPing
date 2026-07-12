# Changelog

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

- README now promotes `composer create-project rith-1437/zeroping` and links to
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
