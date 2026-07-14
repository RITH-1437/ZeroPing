# Release 2.0.0-beta

This beta stabilizes the **Enterprise Framework Foundation** (subsystems
#1–#18) ahead of the `2.0.0` stable release.

## What's new

- Dependency Injection container and Service Providers.
- HTTP Kernel, API Resources and a full Response system.
- Validation, Localization, Cache, Session and File Storage.
- A complete Testing harness (HTTP, database, response assertions).
- Benchmark, Profiler, Debug Toolbar, Security, Logging and Error Handling.
- This Markdown-based documentation viewer.

## Upgrade notes

- The ORM no longer applies `deleted_at IS NULL` to every query. Use
  `softDeletes()`, `withTrashed()` or `onlyTrashed()` to opt in.
- `App::VERSION` is now `2.0.0-beta`.
