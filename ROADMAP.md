# Roadmap

This document outlines the planned direction of the ZeroPing Framework. It is
intentionally high-level; specific scope for each release is decided as work
progresses. Suggestions and contributions are welcome — see
[CONTRIBUTING.md](CONTRIBUTING.md).

## Near term (v1.2)

- **First-party package extraction.** Begin publishing the packages described in
  [`packages/ARCHITECTURE.md`](packages/ARCHITECTURE.md) under the `zeroping/*`
  namespace, starting with `zeroping/support` and `zeroping/queue` as
  Composer-installable components.
- **Expanded test coverage** for the HTTP, Mail, Filesystem, and Scheduling
  subsystems.
- **More queue drivers** (Redis, Beanstalkd) behind the existing contract.
- **Improved error pages** — ✅ shipped in **v1.3.0** (dedicated `403`/`419`
  screens, development `404`/`500` pages with request details and a stack trace,
  and clean production pages).

## Mid term (v1.4+)

- **Asynchronous task scheduling** with crontab-free workers.
- **Localization & internationalization** helpers (`__()` translation loader).
- **API resource transformers** for building JSON APIs with less boilerplate.
- **Event broadcasting** over WebSockets or a queue transport.
- **Official starter kits** (API, blog, admin) built from the existing CLI
  templates.

## Long term (v2.0 and beyond)

- **PHP 8.4+ native features** adoption (property hooks, asymmetric visibility)
  once the minimum supported PHP version allows it.
- **Aura/PSR compatibility** where it adds value without compromising the
  zero-dependency philosophy.
- **Official extensions marketplace** and community package registry.
- **Performance benchmarking dashboard** tracking regressions release over
  release (building on `bench/run.php`).

## Guiding principles

1. **Zero hidden magic.** Everything should be readable and debuggable.
2. **No required runtime dependencies.** The framework runs on PHP alone.
3. **Stable, predictable APIs.** Breaking changes are rare and well-documented.
4. **Documentation first.** No feature ships without docs and tests.
