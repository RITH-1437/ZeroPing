# Roadmap

ZeroPing is actively evolving with a focus on stability first, then ecosystem growth. Suggestions and contributions are welcome — see [CONTRIBUTING.md](CONTRIBUTING.md).

## Guiding Principles

1. **Zero hidden magic.** Everything should be readable and debuggable.
2. **No required runtime dependencies.** The framework runs on PHP alone.
3. **Stable, predictable APIs.** Breaking changes are rare and well-documented.
4. **Documentation first.** No feature ships without docs and tests.

## v1.0.0 — Historical Foundation

The initial stable release that established the foundation:

- Expressive routing system with static/dynamic routes, groups, prefixes, and named routes
- Active Record ORM with relationships, accessors, mutators, and pagination
- Lightweight service container with auto-resolution and singleton binding
- Middleware pipeline for authentication, CSRF, rate limiting, and custom logic
- Blade-style templating engine
- Zero CLI tool for scaffolding and management
- Database migrations with up/down methods and fresh/rollback commands
- Session-based authentication with password hashing and guards
- CSRF protection and security layer
- Session management
- Form request validation
- Caching system

## v1.5.0 — Released

Expanded the framework with relationships, queues, and developer tools:

- Eloquent-style relationships (has-one, has-many, belongs-to, many-to-many)
- Fluent query builder with chainable methods
- Queue and job dispatching with sync and database drivers
- Mail facade with pluggable drivers and mailable classes
- Built-in testing suite with feature and unit testing tools
- Debug bar and profiler for development
- Rate limiting middleware
- API resource transformers

## v2.0.0 — Released

Stability and ecosystem enhancements:

- Real-time broadcasting with WebSocket support
- Localization and i18n framework
- Sliding window rate limiter for improved throttling
- Health check and metrics endpoint
- First-party admin starter kit
- Plugin architecture for extensibility

## v2.0.1 — Current Stable

Polish and quality improvements:

- Premium documentation website with green terminal code blocks
- Expanded documentation coverage (16+ pages)
- Composer package name standardized to `rith-1437/zeroping`
- Version tracking system

## v2.1 — In Progress

Next feature release under active development:

- GraphQL support
- Serverless deployment adapter
- Built-in admin panel generator
- Enhanced async I/O capabilities

## v3.0 — Planned

Long-term vision for advanced features:

- Async and parallel request handling
- Plugin and extension marketplace
- Distributed tracing
- Edge runtime support
