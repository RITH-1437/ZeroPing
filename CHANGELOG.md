# Changelog

All notable changes to the ZeroPing Framework are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [v2.1.0] - 2026-08-01

### Added

- **HTTP Client** — New `App\Core\Http\Client` class and `App\Core\Http\ClientResponse`. Fluent API: `Client::get/post/put/patch/delete()` static methods plus chainable `->withToken()`, `->withHeaders()`, `->withBasicAuth()`, `->timeout()`, `->acceptJson()`, `->baseUrl()`, `->withoutVerifying()`. Response helpers: `ok()`, `failed()`, `json()`, `object()`, `throw()`, status predicate methods. Zero third-party dependencies (uses cURL). `http_client()` global helper added.
- **ORM Eager Loading** — `Model::with('relation')->get()` and `$model->load('relation')` now supported. Eliminates N+1 queries by resolving all four relation types (HasOne, HasMany, BelongsTo, BelongsToMany) with a single batched SQL query per relation.
- **Relation getter methods** — `getForeignKey()`, `getLocalKey()`, `getRelated()`, `getOwnerKey()`, `getPivotTable()`, `getForeignPivotKey()`, `getRelatedPivotKey()` added to all Relation classes to support eager loading.
- **`HasRelationships::setRelation()` / `getRelations()`** — new public methods on the trait for programmatic relation management.
- **QueryBuilder `remember(int $seconds)`** — cache the results of any query with a single fluent call. Auto-derives cache key from SQL + bindings. Gracefully falls back to a live query if cache is unavailable.
- **New helper functions** — `session()`, `csrf_token()`, `csrf_field()`, `abort()`, `now()`, `old()`, `str_plural()`, `str_singular()`, `http_client()`.
- **`config/cors.php`** — dedicated CORS configuration file. Reads `CORS_ALLOWED_ORIGINS` and `CORS_SUPPORTS_CREDENTIALS` from environment. Previously CORS had no config file and the middleware silently used hardcoded defaults for every installation.
- **`config/auth.php`** — authentication configuration file with guards (web/session + api/token), providers, password reset settings, and `password_timeout`.
- 8 new documentation pages: `routing.md`, `middleware.md`, `authentication.md`, `events.md`, `mail.md`, `filesystem.md`, `helpers.md`, `localization.md`. Documentation coverage now 27 pages.
- `declare(strict_types=1)` added to all **60 Console Command files** in `app/Core/Console/Commands/`.
- Code coverage job in CI — produces `coverage.xml` (Clover format) as a build artifact.
- PHP 8.1–8.5 version matrix in CI pipeline.
- `static-analysis` job in CI (PHPStan + PHPCS run as separate steps).
- `static-analysis` composer script alias for local use.
- Ecosystem pages: Showcase, Deploy, Studio, Cloud, Forge (all 5 registered in routes + navbar).
- `ARCHITECTURE.md` — comprehensive 15-section framework internals guide.
- 11 new unit tests covering all 5 new ecosystem website pages.
- `declare(strict_types=1)` added to all ORM Relation classes and `HasRelationships` trait.
- Improved PHPDoc on all ORM Relations (class-level docblocks, usage examples, `@since`, `@throws`, `@param class-string`).

### Fixed

- **`env()` helper falsy-zero bug** — `getenv($key) ?: null` silently converted the legitimate string `"0"` to `null`. Fixed with explicit `!== false` guard.
- **`ray()` helper array-wrap bug** — `(new Dumper())->dump($args)` was wrapping all arguments in an array instead of dumping each one individually. Fixed to loop like `dump()`.
- **`Cors` middleware `Vary: Origin` header missing** — CORS responses were not including `Vary: Origin`, causing CDN caching issues. Fixed.
- **Dead code removed from `NewCommand`** — removed unused methods `ensureParent()`, `installStarterFiles()`, `isFrameworkRepo()` and unused `$createdItems` property.
- **`$skipFiles` dead variable removed from `App::loadConfig()`** — the variable was assigned but never read; a direct string comparison was already handling the skip.
- PHPStan: removed dead `array_values()` wrapper in `DoctorCommand`.
- PHPStan: fixed write-only properties in `InstallerSuccessRenderer` (now rendered in output).
- Removed duplicate `$templates` property in `NewCommand` (DRY fix).
- Trailing whitespace in `config/security.php` (PHPCS compliance).
- `StarterTemplatesTest` updated to use `ReflectionClass::getConstant('TEMPLATES')`.
- PHPCS: `ClientResponse.php` brace formatting auto-corrected.
- PHPCS: `config/auth.php` and `config/cors.php` file header order corrected.

### Changed

- **CI workflow** — added `needs: [build]` dependencies to `mysql-build`, `coverage`, `static-analysis`, and `create-project` jobs (no wasted runner minutes when build fails). Fixed hard-coded GitHub clone URL in `create-project` to use workspace copy instead. Added `--no-coverage` to PHPUnit steps in matrix and mysql-build jobs.
- `composer.json` type changed from `project` to `library`.
- `composer.json` indentation normalized; funding section added; branch alias bumped to `2.1.x-dev`.
- Website homepage ecosystem section expanded: 9 available + 4 coming-soon products.
- Starter template welcome page redesigned with 4-column runtime stats grid.
- Navbar dropdowns expanded with Showcase, Deploy, Studio, Cloud, Forge links.

## [v2.0.1] - 2026-07-17

### Added

- Premium documentation website with responsive layout and green terminal code blocks.
- Expanded documentation coverage (16+ pages including caching, database, container, queues, scheduler, security, extending).
- Composer package name standardized to `rith-1437/zeroping`.
- Version tracking system in `App::VERSION`.

### Fixed

- Code block transparency bug in documentation (5% opacity white background removed).
- DocsService path pointing to wrong directory for markdown files.
- Search route missing controller import.
- Documentation `prose-code` background overriding code block styling.

### Changed

- Homepage hero terminal redesigned with two side-by-side code panels showing `config/routes.php` and `app/Models/User.php`.
- Getting Started page now shows real filenames instead of step titles.
- Installation page documents both Zero CLI and Composer installation methods.
- Footer ASCII logo uses `#` characters via Figlet banner font.
- Browser favicon uses mascot.svg instead of old Z-pattern SVG.

## [v2.0.0] - 2026-07-14

### Added

- Dependency Injection container with automatic resolution and Service Providers.
- HTTP Kernel with middleware pipeline, API Resources, and full Response system.
- Validation engine with extensive rule set and custom rule support.
- Localization (translator, `lang/` files, `trans()` / `__()` helpers).
- Multi-driver Cache (file, array, database) with per-request memory layer.
- Session management with multiple drivers.
- File Storage abstraction with local and extensible drivers.
- Testing harness compatible with PHPUnit with HTTP client, database assertions, and fluent TestResponse.
- Benchmark and Profiler for performance measurement.
- Debug Toolbar with framework-collected telemetry.
- Security (hashing, encryption, secure random, CSRF, rate limiting).
- Logging (multi-channel via Monolog-style handlers).
- Error Handling with pretty exception pages in development.
- Markdown documentation subsystem (`app/Core/Docs`).
- Scheduler with CronExpression parser and mutex support.
- `asset()` and `url()` global helpers.
- `make:auth` scaffolding command.

### Fixed

- ORM soft deletes now opt-in (QueryBuilder no longer appends `deleted_at IS NULL`).
- Router error pages emit correct HTTP status codes (404/500 instead of always 200).
- Docs normalizer preserves dots in slugs.

### Changed

- Environment validation now checks for required keys on install.
- Scheduler hardened with real `isDue()` and mutex support.

## [v1.3.0] - 2026-07-14

### Added

- Console branding with gradient ASCII logo.
- Grouped, colorized command table.
- Rich `php zero about` screen.
- Dedicated 403 and 419 error pages.
- `php zero publish` command for asset customization.
- Per-command `--help` support.
- Environment validation on install with timezone prompt.
- Improved `php zero route:list` with named routes and color-coded methods.

## [v1.2.0] - 2026-07-12

### Added

- Public Composer distribution with `post-create-project-cmd` installer.
- `php zero doctor` for environment verification.
- `php zero about` command.
- `php zero make:test` and `php zero make:command`.
- `php zero serve <port>` with optional port argument.
- Dashboard starter template.
- `.gitattributes` for clean Packagist distribution.

## [v1.1.0] - 2026-07-09

### Added

- Full-text documentation search with fuzzy matching.
- Starter templates (`empty`, `blog`, `api`, `mvc`).
- Enhanced validation with 8 new rules, FluentValidator, and FormRequest.
- Real view, route, and config caching.
- Lazy service loading.

### Fixed

- Multiple security fixes (XSS, command injection, CSRF, random UUIDs).
- Service provider constructor argument mismatches.
- ORM missing contract interfaces.

### Changed

- `View::render()` now returns string instead of void.
- `CSRFToken::get()` no longer regenerates token on every call.
- `PasswordHasher` now extends `Hash`.
- `Session` now extends `SessionGuard`.

## [v1.0.0] - 2026-07-08

### Added

- Initial stable release.
- Expressive routing system.
- Active Record ORM.
- Dependency Injection container.
- Middleware pipeline.
- Blade-style templating engine.
- Zero CLI tool.
- Database migrations.
- Authentication & authorization guards.
- CSRF & security layer.
- Session management.
- Form request validation.
- Caching system.

[Unreleased]: https://github.com/rith-1437/ZeroPing/compare/v2.1.0...HEAD
[v2.1.0]: https://github.com/rith-1437/ZeroPing/compare/v2.0.1...v2.1.0
[v2.0.1]: https://github.com/rith-1437/ZeroPing/compare/v2.0.0...v2.0.1
[v2.0.0]: https://github.com/rith-1437/ZeroPing/compare/v1.3.0...v2.0.0
[v1.3.0]: https://github.com/rith-1437/ZeroPing/compare/v1.2.0...v1.3.0
[v1.2.0]: https://github.com/rith-1437/ZeroPing/compare/v1.1.0...v1.2.0
[v1.1.0]: https://github.com/rith-1437/ZeroPing/compare/v1.0.0...v1.1.0
[v1.0.0]: https://github.com/rith-1437/ZeroPing/releases/tag/v1.0.0
