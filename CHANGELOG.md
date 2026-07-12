# Changelog

All notable changes to the ZeroPing Framework are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-07-12

### Added
- Official documentation website under `docs/` covering installation, routing,
  the container, database, ORM, validation, caching, queues, scheduling,
  security, testing, deployment, and the full CLI reference.
- Package ecosystem foundation under `packages/`, including a design document
  (`packages/ARCHITECTURE.md`) outlining the 12 planned first-party packages.
- Reference implementations for `zeroping/support` (base `ServiceProvider`,
  `CommandRegistry`, `MigrationLoader`, `ViewFinder`) and `zeroping/queue`
  (queue contract, `SyncJob`, and `Worker`).
- `SECURITY.md`, `CODE_OF_CONDUCT.md`, and `ROADMAP.md` community documents.
- GitHub Actions CI workflow (`.github/workflows/ci.yml`) that boots the
  framework, runs migrations, exercises CLI commands, and executes the test
  suite on every push and pull request.

### Changed
- Significant performance pass across core subsystems (see `PERFORMANCE.md`):
  - Container reflection caching and a resolved-singleton map.
  - Router compiled route patterns, a named-route map, and middleware caching.
  - Config lazy cache reads and per-key memoization.
  - Env compiled cache to avoid repeated parsing.
  - Cache repository per-request in-memory layer.
  - ORM accessor/mutator memoization.
  - Session started-flag guard and View path caching.
  - Conditional database query profiling.
  - Optimized Composer classmap autoloading.
- Standardized PHPDoc blocks and naming across core services for readability
  and consistency.
- `composer.json` cleaned up and validated with `composer validate --strict`.

### Fixed
- Invalid empty `require` entry in `composer.json` that broke
  `composer validate --strict`.
- Session re-entrancy issue where `session_start()` could be called more than
  once.
- Various documentation and CLI stub consistency improvements.

### Removed
- Redundant duplicated helper/boilerplate code consolidated into shared core
  utilities.

## [1.0.0] - 2023

- Initial public release of the ZeroPing Framework: MVC core, routing,
  dependency-injection container, ORM, schema/migrations, validation, caching,
  queue, scheduler, security middleware, and the `php zero` CLI.

[Unreleased]: https://github.com/RITH-1437/ZeroPing/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/RITH-1437/ZeroPing/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/RITH-1437/ZeroPing/releases/tag/v1.0.0
