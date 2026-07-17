# Changelog

All notable changes to the ZeroPing Framework are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[v2.0.1]: https://github.com/rith-1437/ZeroPing/compare/v2.0.0...v2.0.1
[v2.0.0]: https://github.com/rith-1437/ZeroPing/compare/v1.3.0...v2.0.0
[v1.3.0]: https://github.com/rith-1437/ZeroPing/compare/v1.2.0...v1.3.0
[v1.2.0]: https://github.com/rith-1437/ZeroPing/compare/v1.1.0...v1.2.0
[v1.1.0]: https://github.com/rith-1437/ZeroPing/compare/v1.0.0...v1.1.0
[v1.0.0]: https://github.com/rith-1437/ZeroPing/releases/tag/v1.0.0
