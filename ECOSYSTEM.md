# ZeroPing Arena — Ecosystem Architecture

> **Version:** 1.0.0-draft  
> **Date:** 2026-07-29  
> **Author:** Product Lead  
> **Status:** Design Document  

ZeroPing Arena is the complete developer platform surrounding the ZeroPing PHP framework.
This document defines the ecosystem architecture, roadmap, and implementation plan.

---

## Table of Contents

1. [Ecosystem Architecture](#1-ecosystem-architecture)
2. [Feature Roadmap](#2-feature-roadmap)
3. [Folder Structure](#3-folder-structure)
4. [Required Repositories](#4-required-repositories)
5. [Official Packages](#5-official-packages)
6. [Future Products](#6-future-products)
7. [Integration Strategy](#7-integration-strategy)
8. [Migration Plan](#8-migration-plan)
9. [Prioritized Implementation Order](#9-prioritized-implementation-order)

---

## 1. Ecosystem Architecture

### 1.1 Philosophy

| Principle | Description |
|-----------|-------------|
| **Lightweight core** | The framework ships only what every app needs. Everything else is opt-in. |
| **Contract-first** | Every subsystem exposes interfaces. Packages implement contracts. |
| **Provider-driven** | All wiring happens via `register()`/`boot()`. No hidden magic. |
| **Zero-config start** | `php zero new app` gives a working app with SQLite. No external services. |
| **Progressive complexity** | Start simple, add packages as you grow. Never pay for what you don't use. |
| **Composer-native** | All packages are standard Composer packages. No proprietary tooling lock-in. |

### 1.2 Ecosystem Layers

```
┌─────────────────────────────────────────────────────────────────────┐
│                        ZEROPING ARENA CLOUD                         │
│         (Deploy · Monitor · Scale · Managed Infrastructure)         │
├─────────────────────────────────────────────────────────────────────┤
│                         ARENA MARKETPLACE                           │
│    (Package Registry · Templates · Extensions · Community)          │
├─────────────────────────────────────────────────────────────────────┤
│                      DEVELOPER TOOLS LAYER                          │
│  Zero CLI · Arena Studio · Debug Bar · Profiler · Documentation     │
├─────────────────────────────────────────────────────────────────────┤
│                      OFFICIAL PACKAGES LAYER                        │
│  Auth · Mail · Queue · Storage · Notifications · Search · Admin     │
├─────────────────────────────────────────────────────────────────────┤
│                       FRAMEWORK CORE (zeroping/framework)           │
│  Container · Router · ORM · Validation · Cache · Session · Events   │
├─────────────────────────────────────────────────────────────────────┤
│                       FOUNDATION (zeroping/support)                  │
│  ServiceProvider · CommandRegistry · Contracts · Helpers · Testing   │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.3 Core Components Map

| Component | Location | Role |
|-----------|----------|------|
| **Zero CLI** | `zeroping/cli` | Project scaffolding, code generation, package management |
| **Framework Core** | `zeroping/framework` | DI, Router, ORM, Validation, Cache, Session, Events, Http |
| **Support** | `zeroping/support` | Base ServiceProvider, helpers, contracts, collection utilities |
| **Arena Registry** | `arena.zeroping.dev` | Package marketplace and distribution |
| **Arena Cloud** | `cloud.zeroping.dev` | Deployment, hosting, monitoring (future) |
| **Arena Studio** | `studio.zeroping.dev` | Web-based admin panel and dashboard (future) |

### 1.4 Package Discovery Flow

```
composer require zeroping/auth
        │
        ▼
composer.json → extra.zeroping.providers: ["Zeroping\Auth\AuthServiceProvider"]
        │
        ▼
post-autoload-dump → scripts/discover-packages.php
        │
        ▼
bootstrap/cache/packages.php (manifest updated)
        │
        ▼
App::boot() → ProviderRepository merges discovered providers
        │
        ▼
AuthServiceProvider::register() → binds contracts
AuthServiceProvider::boot() → routes, migrations, views, commands
```

### 1.5 Starter Kit Architecture

Starter kits are pre-configured project templates combining multiple packages:

| Kit | Packages Included | Use Case |
|-----|-------------------|----------|
| `empty` | support only | Minimal starting point |
| `api` | auth, validation, rate-limiter, api-resources | REST API backend |
| `blog` | auth, mail, pagination, search | Content-driven app |
| `mvc` | auth, validation, pagination, mail | Full-stack web app |
| `starter` | auth, admin, mail, queue, scheduler | Production-ready app |
| `arena` (future) | All official packages | Full ecosystem demo |
| `saas` (future) | auth, billing, admin, queue, mail, notifications | SaaS boilerplate |



---

## 2. Feature Roadmap

### v2.1 — Package Extraction & Registry (Q3 2026)

**Theme:** Extract core features into installable packages. Launch package registry.

| Feature | Type | Priority |
|---------|------|----------|
| Extract `zeroping/auth` package | Package | P0 |
| Extract `zeroping/mail` package | Package | P0 |
| Extract `zeroping/notifications` package | Package | P0 |
| Extract `zeroping/storage` package | Package | P1 |
| Extract `zeroping/scheduler` package | Package | P1 |
| Package registry MVP (arena.zeroping.dev) | Infrastructure | P0 |
| `php zero package:publish` improvements | CLI | P1 |
| `php zero package:search` command | CLI | P1 |
| Authentication starter kit (`zeroping/auth-kit`) | Kit | P0 |
| Admin panel foundation (`zeroping/admin`) | Package | P1 |
| Queue dashboard (web UI) | Package | P2 |
| Scheduler dashboard (web UI) | Package | P2 |
| Improved debug toolbar with package info | Enhancement | P2 |

### v2.2 — Developer Experience & Tooling (Q4 2026)

**Theme:** World-class developer tools. First-class testing. Documentation generation.

| Feature | Type | Priority |
|---------|------|----------|
| `zeroping/testing` package with factories, fakers | Package | P0 |
| API documentation generator (OpenAPI/Swagger) | Tool | P1 |
| `php zero docs:generate` command | CLI | P1 |
| `php zero make:crud` full CRUD scaffolding | CLI | P1 |
| `php zero make:api` API resource scaffolding | CLI | P1 |
| Interactive `php zero tinker` REPL | CLI | P2 |
| Hot-reload dev server (`php zero serve --watch`) | CLI | P1 |
| Release tooling (`php zero release`) | CLI | P2 |
| Package publishing to registry | CLI | P1 |
| Arena website: downloads page | Website | P1 |
| Arena website: documentation search improvements | Website | P2 |
| Arena website: package browser | Website | P1 |

### v2.5 — Ecosystem Maturity (Q1 2027)

**Theme:** Complete official package suite. Community marketplace. Production tooling.

| Feature | Type | Priority |
|---------|------|----------|
| `zeroping/search` (full-text search abstraction) | Package | P1 |
| `zeroping/api-resources` (standalone) | Package | P1 |
| `zeroping/rate-limiter` (standalone) | Package | P1 |
| `zeroping/pagination` (standalone) | Package | P1 |
| `zeroping/localization` (standalone) | Package | P1 |
| Community package submissions to registry | Infrastructure | P1 |
| Arena website: community showcase | Website | P2 |
| Arena website: tutorials section | Website | P2 |
| Arena website: extension marketplace | Website | P1 |
| Package quality scoring and badges | Infrastructure | P2 |
| `php zero deploy` command (basic) | CLI | P2 |
| Version management tooling | CLI | P2 |
| Websocket support (`zeroping/websocket`) | Package | P2 |

### v3.0 — Arena Cloud & Advanced Features (Q2 2027)

**Theme:** Managed deployment. Async capabilities. Performance at scale.

| Feature | Type | Priority |
|---------|------|----------|
| Arena Cloud MVP (managed hosting) | Product | P1 |
| Arena Forge (server provisioning) | Product | P2 |
| `php zero deploy --production` | CLI | P1 |
| Async queue drivers (Redis, RabbitMQ) | Package | P0 |
| Redis cache/session drivers | Package | P0 |
| Database connection pooling | Core | P1 |
| Request/response streaming | Core | P2 |
| Distributed event bus | Package | P2 |
| Arena Studio (web admin panel) | Product | P1 |
| Edge runtime adapter | Core | P3 |
| Health checks and metrics endpoint | Package | P1 |
| Arena website: learning paths | Website | P2 |
| Arena website: sponsors program | Website | P2 |

### v4.0 — Platform Scale (Q4 2027)

**Theme:** Enterprise-ready. Multi-tenancy. Microservices. Global edge.

| Feature | Type | Priority |
|---------|------|----------|
| Multi-tenancy package (`zeroping/tenancy`) | Package | P1 |
| Service mesh / microservice communication | Package | P2 |
| GraphQL adapter (`zeroping/graphql`) | Package | P2 |
| Serverless adapter (AWS Lambda, Vercel) | Package | P1 |
| Arena Vapor (serverless deployment) | Product | P1 |
| Distributed tracing and observability | Package | P2 |
| Feature flags package | Package | P2 |
| AI/ML integration helpers | Package | P3 |
| Arena website: enterprise page | Website | P2 |
| Arena website: certification program | Website | P3 |



---

## 3. Folder Structure

### 3.1 Mono-repo (Development)

The primary development repository contains all packages for synchronized development:

```
zeroping/
├── .github/
│   ├── workflows/
│   │   ├── ci.yml                    # Test all packages
│   │   ├── split.yml                 # Split mono-repo to read-only repos
│   │   └── release.yml               # Tag and release packages
│   └── ISSUE_TEMPLATE/
├── packages/
│   ├── zeroping/
│   │   ├── support/                  # Base: ServiceProvider, helpers, contracts
│   │   │   ├── src/
│   │   │   ├── config/
│   │   │   ├── tests/
│   │   │   └── composer.json
│   │   ├── framework/                # Core: Container, Router, ORM, Http, etc.
│   │   │   ├── src/
│   │   │   │   ├── Container/
│   │   │   │   ├── Routing/
│   │   │   │   ├── Database/
│   │   │   │   ├── Http/
│   │   │   │   ├── Validation/
│   │   │   │   ├── Cache/
│   │   │   │   ├── Session/
│   │   │   │   ├── Config/
│   │   │   │   └── Events/
│   │   │   ├── tests/
│   │   │   └── composer.json
│   │   ├── auth/                     # Authentication & authorization
│   │   │   ├── src/
│   │   │   │   ├── Contracts/
│   │   │   │   ├── Guards/
│   │   │   │   ├── Middleware/
│   │   │   │   ├── Console/
│   │   │   │   └── AuthServiceProvider.php
│   │   │   ├── config/
│   │   │   ├── database/migrations/
│   │   │   ├── routes/
│   │   │   ├── views/
│   │   │   ├── tests/
│   │   │   └── composer.json
│   │   ├── mail/                     # Mailing with drivers
│   │   ├── queue/                    # Queue with drivers
│   │   ├── notifications/            # Multi-channel notifications
│   │   ├── storage/                  # Filesystem abstraction
│   │   ├── scheduler/                # Task scheduling
│   │   ├── search/                   # Full-text search
│   │   ├── localization/             # i18n translation
│   │   ├── pagination/               # Cursor & offset pagination
│   │   ├── rate-limiter/             # Rate limiting
│   │   ├── api-resources/            # API resource transformers
│   │   ├── testing/                  # Test utilities, factories, fakers
│   │   ├── admin/                    # Admin panel
│   │   └── debug/                    # Debug toolbar & profiler
├── starters/
│   ├── empty/
│   ├── blog/
│   ├── api/
│   ├── mvc/
│   ├── starter/
│   └── saas/
├── tools/
│   ├── cli/                          # Zero CLI (standalone installer)
│   ├── arena-registry/               # Registry server
│   └── docs-generator/               # API documentation generator
├── website/
│   ├── framework-site/               # Main documentation site
│   ├── arena-marketplace/            # Package marketplace
│   └── arena-cloud-console/          # Cloud management UI (future)
├── composer.json                     # Root mono-repo composer
├── monorepo-builder.php              # Split configuration
└── ECOSYSTEM.md                      # This document
```

### 3.2 Individual Package Structure (Standard)

Every official package follows this canonical structure:

```
zeroping/<package-name>/
├── composer.json
├── LICENSE
├── README.md
├── CHANGELOG.md
├── config/
│   └── <package>.php               # Default configuration
├── database/
│   └── migrations/                 # Package migrations (if any)
├── resources/
│   ├── views/                      # Namespaced views (if any)
│   └── lang/                       # Translation files (if any)
├── routes/
│   ├── web.php                     # Web routes (if any)
│   └── api.php                     # API routes (if any)
├── src/
│   ├── Contracts/                  # Interfaces (always present)
│   │   └── <Contract>.php
│   ├── Console/                    # CLI commands (if any)
│   │   └── <Command>Command.php
│   ├── Middleware/                 # HTTP middleware (if any)
│   ├── Exceptions/                 # Package-specific exceptions
│   ├── <Implementation>.php        # Concrete implementations
│   └── <Package>ServiceProvider.php # The service provider
├── stubs/                          # Publishable stubs
└── tests/
    ├── Unit/
    └── Feature/
```

### 3.3 Application Structure (End User)

When a user creates a new ZeroPing project:

```
my-app/
├── app/
│   ├── Controllers/
│   ├── Models/
│   ├── Services/
│   ├── Middleware/
│   ├── Providers/
│   ├── Jobs/
│   ├── Mail/
│   ├── Events/
│   ├── Listeners/
│   ├── Notifications/
│   └── Exceptions/
├── bootstrap/
│   ├── app.php
│   └── cache/
├── config/
│   ├── app.php
│   ├── database.php
│   ├── cache.php
│   ├── mail.php
│   ├── queue.php
│   ├── packages.php
│   └── ...
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── database.sqlite
├── lang/
├── public/
├── resources/
├── routes/
│   ├── web.php
│   └── api.php
├── storage/
│   ├── cache/
│   ├── logs/
│   └── uploads/
├── stubs/
├── tests/
├── views/
├── vendor/
├── .env
├── composer.json
├── phpunit.xml
└── zero                            # CLI entry point
```



---

## 4. Required Repositories

### 4.1 Core Repositories

| Repository | Packagist Name | Description | Priority |
|------------|---------------|-------------|----------|
| `zeroping/zeroping` | — | Mono-repo (development only, not published) | P0 |
| `zeroping/framework` | `zeroping/framework` | Core framework (split from mono-repo) | P0 |
| `zeroping/support` | `zeroping/support` | Base package (ServiceProvider, contracts, helpers) | P0 |
| `zeroping/cli` | `zeroping/cli` | Standalone CLI installer tool | P0 |
| `zeroping/skeleton` | `zeroping/skeleton` | Default project skeleton (create-project target) | P0 |

### 4.2 Official Package Repositories (read-only splits)

| Repository | Packagist Name | Description | Priority |
|------------|---------------|-------------|----------|
| `zeroping/auth` | `zeroping/auth` | Authentication & authorization | P0 |
| `zeroping/mail` | `zeroping/mail` | Email sending with drivers | P0 |
| `zeroping/queue` | `zeroping/queue` | Background job processing | P0 |
| `zeroping/notifications` | `zeroping/notifications` | Multi-channel notifications | P1 |
| `zeroping/storage` | `zeroping/storage` | Filesystem abstraction | P1 |
| `zeroping/scheduler` | `zeroping/scheduler` | Task scheduling | P1 |
| `zeroping/search` | `zeroping/search` | Full-text search | P2 |
| `zeroping/localization` | `zeroping/localization` | i18n translation system | P2 |
| `zeroping/pagination` | `zeroping/pagination` | Cursor & offset pagination | P2 |
| `zeroping/rate-limiter` | `zeroping/rate-limiter` | Request rate limiting | P2 |
| `zeroping/api-resources` | `zeroping/api-resources` | API resource transformers | P2 |
| `zeroping/testing` | `zeroping/testing` | Test utilities and factories | P1 |
| `zeroping/admin` | `zeroping/admin` | Admin panel | P2 |
| `zeroping/debug` | `zeroping/debug` | Debug toolbar & profiler | P2 |

### 4.3 Starter Kit Repositories

| Repository | Description | Priority |
|------------|-------------|----------|
| `zeroping/starter-empty` | Minimal skeleton | P0 |
| `zeroping/starter-api` | API-focused project | P0 |
| `zeroping/starter-blog` | Blog with auth and content | P1 |
| `zeroping/starter-mvc` | Traditional web app | P1 |
| `zeroping/starter-saas` | SaaS boilerplate (future) | P3 |

### 4.4 Infrastructure Repositories

| Repository | Description | Priority |
|------------|-------------|----------|
| `zeroping/arena-registry` | Package registry server (Satis-based + custom API) | P1 |
| `zeroping/arena-website` | Documentation & marketing site | P0 |
| `zeroping/docs` | Documentation source files | P1 |
| `zeroping/arena-cloud` | Cloud deployment platform (future) | P3 |
| `zeroping/arena-studio` | Web-based admin/management UI (future) | P3 |

### 4.5 Repository Split Strategy

Development happens in the mono-repo. GitHub Actions splits each `packages/zeroping/*`
directory into its own read-only repository on every push to `main`:

```yaml
# .github/workflows/split.yml
name: Split Packages
on:
  push:
    branches: [main]
jobs:
  split:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        package:
          - { name: "support", repo: "zeroping/support" }
          - { name: "framework", repo: "zeroping/framework" }
          - { name: "auth", repo: "zeroping/auth" }
          - { name: "mail", repo: "zeroping/mail" }
          - { name: "queue", repo: "zeroping/queue" }
          # ... all packages
    steps:
      - uses: actions/checkout@v4
      - uses: symplify/monorepo-split-github-action@v2
        with:
          package_directory: packages/zeroping/${{ matrix.package.name }}
          repository_organization: zeroping
          repository_name: ${{ matrix.package.name }}
```



---

## 5. Official Packages

### 5.1 Package Overview

| # | Package | Namespace | Core Contracts | Drivers/Channels |
|---|---------|-----------|---------------|-----------------|
| 1 | `zeroping/support` | `Zeroping\Support` | ServiceProvider, Collection, Str, Arr | — |
| 2 | `zeroping/auth` | `Zeroping\Auth` | Guard, UserProvider, Authenticatable | Session, Token, JWT |
| 3 | `zeroping/mail` | `Zeroping\Mail` | Mailer, Mailable, Transport | SMTP, Mailgun, SES, Log |
| 4 | `zeroping/queue` | `Zeroping\Queue` | Queue, Job, Worker | Sync, Database, Redis |
| 5 | `zeroping/notifications` | `Zeroping\Notifications` | Notification, Channel, Notifiable | Mail, Database, SMS |
| 6 | `zeroping/storage` | `Zeroping\Storage` | Filesystem, Disk | Local, S3, FTP |
| 7 | `zeroping/scheduler` | `Zeroping\Scheduler` | Schedule, Event, Mutex | — |
| 8 | `zeroping/search` | `Zeroping\Search` | SearchEngine, Searchable, Builder | Database, Meilisearch, Algolia |
| 9 | `zeroping/localization` | `Zeroping\Localization` | Translator, Loader | File, Database |
| 10 | `zeroping/pagination` | `Zeroping\Pagination` | Paginator, CursorPaginator | — |
| 11 | `zeroping/rate-limiter` | `Zeroping\RateLimiter` | Limiter, Store | Cache, Database |
| 12 | `zeroping/api-resources` | `Zeroping\ApiResources` | Resource, ResourceCollection | — |
| 13 | `zeroping/testing` | `Zeroping\Testing` | TestCase, Factory, Faker | — |
| 14 | `zeroping/admin` | `Zeroping\Admin` | Panel, Page, Widget | — |
| 15 | `zeroping/debug` | `Zeroping\Debug` | DebugBar, Collector, Profiler | — |

### 5.2 Package Details

#### `zeroping/auth` — Authentication & Authorization

```php
// composer.json (partial)
{
    "name": "zeroping/auth",
    "require": {
        "php": ">=8.1",
        "zeroping/support": "^2.1"
    },
    "extra": {
        "zeroping": {
            "providers": ["Zeroping\\Auth\\AuthServiceProvider"]
        }
    }
}
```

**Features:**
- Session-based authentication (login, logout, remember me)
- Token-based API authentication (Bearer tokens)
- JWT guard (optional driver)
- Authorization policies and gates
- Password reset flow
- Email verification flow
- `make:auth` scaffolding (controllers, views, routes)
- Middleware: `auth`, `guest`, `verified`, `can`

**Config (`config/auth.php`):**
```php
return [
    'defaults' => ['guard' => 'web'],
    'guards' => [
        'web' => ['driver' => 'session', 'provider' => 'users'],
        'api' => ['driver' => 'token', 'provider' => 'users'],
    ],
    'providers' => [
        'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],
    ],
    'passwords' => [
        'users' => ['table' => 'password_resets', 'expire' => 60],
    ],
];
```

#### `zeroping/mail` — Email

**Features:**
- Mailable classes with fluent API
- Multiple driver support (SMTP, Mailgun, SES, Postmark, Log)
- Markdown email templates
- Queueable emails (`implements ShouldQueue`)
- Mail preview in development
- `php zero make:mail` command

#### `zeroping/queue` — Background Jobs

**Features:**
- Job dispatching with `dispatch()` helper
- Multiple drivers: sync, database, Redis (v3.0)
- Delayed jobs, retries, max attempts
- Failed job tracking and retry
- Job batching (v2.5)
- Worker process management
- `php zero queue:work`, `queue:retry`, `queue:failed`
- Queue dashboard (web UI, v2.1)

#### `zeroping/notifications` — Multi-Channel Notifications

**Features:**
- Send via mail, database, SMS, Slack, custom channels
- Notifiable trait for models
- Notification history (database channel)
- Queued notifications
- `php zero make:notification` command

#### `zeroping/storage` — Filesystem

**Features:**
- Unified API across drivers (local, S3, FTP)
- File upload handling with validation
- Temporary URLs (S3)
- File visibility (public/private)
- Streaming large files
- `php zero storage:link` for public symlinks

#### `zeroping/scheduler` — Task Scheduling

**Features:**
- Cron expression support
- Fluent frequency API (`->daily()`, `->hourly()`, `->everyFiveMinutes()`)
- Mutex/overlap prevention
- Output logging
- Ping hooks (before/after URLs)
- `php zero schedule:run` (single cron entry)
- `php zero schedule:list`

#### `zeroping/admin` — Admin Panel

**Features:**
- Auto-generated CRUD for models
- Dashboard with widgets (stats, charts, recent activity)
- User management interface
- Queue monitoring widget
- Scheduler status widget
- Customizable via Blade-style views
- Role-based access control integration with `zeroping/auth`

#### `zeroping/testing` — Test Utilities

**Features:**
- Model factories with Faker integration
- Database seeders
- HTTP test client with fluent assertions
- Queue/Mail/Notification fakes
- Time manipulation helpers
- Browser testing adapter (Panther-style)
- `php zero make:factory`, `php zero make:test`



---

## 6. Future Products

### 6.1 Product Suite Overview

```
┌──────────────────────────────────────────────────────────────┐
│                    ZEROPING ARENA PLATFORM                    │
├────────────────┬────────────────┬────────────────────────────┤
│  Arena Cloud   │  Arena Forge   │     Arena Vapor            │
│  (Hosting)     │  (Servers)     │     (Serverless)           │
├────────────────┼────────────────┼────────────────────────────┤
│  Arena Studio  │  Arena Pulse   │     Arena Registry         │
│  (Admin UI)    │  (Monitoring)  │     (Marketplace)          │
└────────────────┴────────────────┴────────────────────────────┘
```

### 6.2 Arena Cloud (Managed Hosting)

**Comparable to:** Laravel Cloud, Vercel, Railway

**Scope:**
- One-click deployment from Git repositories
- Automatic SSL, domains, and CDN
- Database provisioning (SQLite, MySQL, PostgreSQL)
- Environment variable management
- Zero-downtime deployments
- Auto-scaling based on traffic
- Integrated logging and metrics

**CLI Integration:**
```bash
php zero cloud:login
php zero cloud:deploy
php zero cloud:env set DB_HOST=...
php zero cloud:logs --tail
php zero cloud:rollback
```

**Revenue model:** Usage-based (compute + storage + bandwidth)

### 6.3 Arena Forge (Server Provisioning)

**Comparable to:** Laravel Forge, Ploi

**Scope:**
- Provision servers on DigitalOcean, AWS, Hetzner, Vultr
- Automated Nginx/PHP/MySQL/Redis/Supervisor setup
- SSL certificate management (Let's Encrypt)
- Deployment scripts and Git hooks
- Worker/queue process management
- Server monitoring and alerts
- Team access controls

**Target:** v3.0+

### 6.4 Arena Vapor (Serverless)

**Comparable to:** Laravel Vapor, Bref

**Scope:**
- Deploy ZeroPing apps to AWS Lambda
- Automatic API Gateway configuration
- SQS-backed queue processing
- S3 asset serving
- CloudFront CDN integration
- Aurora Serverless database
- Zero infrastructure management

**Target:** v4.0

### 6.5 Arena Studio (Web Admin)

**Comparable to:** Laravel Nova, Filament

**Scope:**
- Model-driven admin panel builder
- CRUD generation from model definitions
- Dashboard widgets (KPIs, charts, tables)
- Form builder with validation
- File manager
- User/role management
- Activity log viewer
- Customizable themes

**Target:** v3.0 (standalone package first at v2.1 as `zeroping/admin`)

### 6.6 Arena Pulse (Monitoring)

**Comparable to:** Laravel Pulse, Sentry

**Scope:**
- Real-time application metrics
- Slow query detection
- Exception tracking
- Queue throughput monitoring
- Cache hit rates
- User activity tracking
- Custom dashboards
- Alerts and notifications

**Target:** v3.0+

### 6.7 Arena Registry (Package Marketplace)

**Comparable to:** Packagist + Laravel Nova marketplace

**Scope:**
- Official and community packages
- Quality scoring (tests, docs, maintenance)
- Download statistics
- Security vulnerability scanning
- Paid package support (commercial packages)
- Featured packages and curated collections
- Package reviews and ratings
- Integration with `php zero package:search`

**Target:** MVP at v2.1, full marketplace at v2.5



---

## 7. Integration Strategy

### 7.1 How Packages Integrate with the Framework

All integration flows through three mechanisms:

#### Mechanism 1: Package Discovery (Automatic)

```
composer require zeroping/auth
    → composer.json declares extra.zeroping.providers
    → post-autoload-dump triggers discover-packages.php
    → bootstrap/cache/packages.php updated
    → Next request: provider auto-registered
```

**No user action required.** The package is active immediately.

#### Mechanism 2: Service Provider Registration (Manual)

For packages that need explicit opt-in or configuration:

```php
// config/app.php
'providers' => [
    // ... core providers
    Zeroping\Auth\AuthServiceProvider::class,
],
```

#### Mechanism 3: Configuration Publishing

Packages ship default configs. Users can override:

```bash
php zero vendor:publish --provider="Zeroping\Auth\AuthServiceProvider"
# Publishes config/auth.php, database/migrations/*, views/auth/*
```

### 7.2 CLI Integration

Every official package can register CLI commands:

```php
class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            MakeAuthCommand::class,      // php zero make:auth
            AuthStatusCommand::class,    // php zero auth:status
        ]);
    }
}
```

Commands are discovered automatically via `CommandRegistry`.

### 7.3 Cross-Package Communication

Packages communicate through:

| Method | Use Case | Example |
|--------|----------|---------|
| **Events** | Loose coupling between packages | Auth fires `UserRegistered`, Notifications listens |
| **Contracts** | Depend on interfaces, not implementations | Queue depends on `CacheContract` for rate limiting |
| **Container** | Resolve any registered service | Admin resolves `AuthManager` to show users |
| **Config** | Shared configuration keys | Mail reads `config('queue.default')` for async sending |

### 7.4 Dependency Graph (Official Packages)

```
zeroping/support (base — all packages depend on this)
    │
    ├── zeroping/framework (core — depends on support)
    │       │
    │       ├── zeroping/auth (depends on framework)
    │       ├── zeroping/mail (depends on framework)
    │       ├── zeroping/queue (depends on framework)
    │       ├── zeroping/storage (depends on framework)
    │       ├── zeroping/scheduler (depends on framework)
    │       ├── zeroping/search (depends on framework)
    │       ├── zeroping/localization (depends on framework)
    │       ├── zeroping/pagination (depends on framework)
    │       ├── zeroping/rate-limiter (depends on framework)
    │       ├── zeroping/api-resources (depends on framework)
    │       └── zeroping/testing (depends on framework)
    │
    ├── zeroping/notifications (depends on framework + mail)
    ├── zeroping/admin (depends on framework + auth)
    └── zeroping/debug (depends on framework)
```

### 7.5 Website & Registry Integration

```
Arena Website (zero-ping.duckdns.org)
    │
    ├── /docs           → Documentation (markdown rendered)
    ├── /packages       → Registry browser (API to arena-registry)
    ├── /downloads      → CLI installer + Composer instructions
    ├── /community      → Discussions, showcase, contributors
    ├── /templates      → Starter kit gallery
    ├── /tutorials      → Learning resources
    ├── /roadmap        → Public roadmap
    └── /sponsors       → Sponsorship tiers
```

The registry exposes a JSON API consumed by:
- The website package browser
- `php zero package:search <query>`
- `php zero package:install <name>`

### 7.6 Versioning Strategy

| Component | Versioning | Compatibility |
|-----------|-----------|---------------|
| Framework core | Semantic (2.x, 3.x) | Breaking changes only at major versions |
| Official packages | Match framework major version | `zeroping/auth:^2.1` works with framework `2.*` |
| CLI | Independent semver | Backward-compatible with framework 2.x+ |
| Starters | Tagged per framework version | `2.1`, `2.2`, etc. |
| Arena Cloud | Continuous deployment | Always supports latest stable framework |



---

## 8. Migration Plan

### 8.1 Overview

Transition from the current monolithic `app/Core/*` structure to extracted packages,
while maintaining backward compatibility for existing users.

### 8.2 Migration Phases

#### Phase 1: Foundation (v2.0.x → v2.1)

**Goal:** Establish the package infrastructure without breaking existing apps.

| Step | Action | Risk |
|------|--------|------|
| 1 | Finalize `zeroping/support` package (ServiceProvider base, helpers) | Low |
| 2 | Create `zeroping/framework` package containing current `app/Core/*` | Medium |
| 3 | Add backward-compatibility layer: `app/Core/*` classes extend package classes | Low |
| 4 | Extract `zeroping/auth` from `app/Core/Auth` + `app/Core/Security` (partial) | Medium |
| 5 | Extract `zeroping/mail` from `app/Core/Mail` | Low |
| 6 | Extract `zeroping/queue` from `app/Core/Queue` | Low |
| 7 | Update `composer.json` to require extracted packages | Low |
| 8 | Ensure `php zero new` still works identically | Critical |

**Backward Compatibility Strategy:**
```php
// app/Core/Auth/AuthManager.php (after extraction)
namespace App\Core\Auth;

// This file becomes a thin alias for backward compat
class_alias(\Zeroping\Auth\AuthManager::class, 'App\Core\Auth\AuthManager');
```

Or using PSR-4 autoload redirection in the framework package.

#### Phase 2: Extraction (v2.1 → v2.2)

**Goal:** Complete extraction of all subsystems into standalone packages.

| Step | Action | Depends On |
|------|--------|-----------|
| 1 | Extract `zeroping/notifications` from `app/Core/Notifications` | Phase 1 |
| 2 | Extract `zeroping/storage` from `app/Core/Filesystem` | Phase 1 |
| 3 | Extract `zeroping/scheduler` from `app/Core/Scheduling` | Phase 1 |
| 4 | Extract `zeroping/localization` from `app/Core/Localization` | Phase 1 |
| 5 | Extract `zeroping/debug` from `app/Core/Debug` + `Profiler` + `Benchmark` | Phase 1 |
| 6 | Extract `zeroping/testing` from `app/Core/Testing` | Phase 1 |
| 7 | Create `zeroping/pagination` (new, from ORM pagination) | Phase 1 |
| 8 | Create `zeroping/rate-limiter` (from Security rate limiter) | Phase 1 |
| 9 | Create `zeroping/api-resources` (from Http/Resources) | Phase 1 |
| 10 | Deprecate direct `app/Core/*` usage in documentation | Phase 1 |

#### Phase 3: Optimization (v2.2 → v2.5)

**Goal:** Remove legacy compatibility layer. Clean architecture.

| Step | Action |
|------|--------|
| 1 | Remove `app/Core/*` backward-compat aliases (deprecated since v2.1) |
| 2 | Framework core (`zeroping/framework`) contains only Container, Router, Http, ORM, Config, Events |
| 3 | All other features are opt-in packages |
| 4 | Default project skeleton requires only `zeroping/framework` + selected packages |
| 5 | Update all starter templates to use package-based architecture |

#### Phase 4: Independence (v3.0)

**Goal:** Packages are fully independent. Framework core is minimal.

| Step | Action |
|------|--------|
| 1 | `zeroping/framework` is ~15 files (Container, Router, Http, ORM kernel, Events, Config) |
| 2 | Every other feature is a separate Composer package |
| 3 | Users install only what they need |
| 4 | Framework boots in < 5ms with zero packages |
| 5 | Full package suite available for those who want Laravel-like completeness |

### 8.3 User Migration Guide (v2.0 → v2.1)

For existing v2.0 users, the upgrade is non-breaking:

```bash
# 1. Update framework
composer require zeroping/framework:^2.1

# 2. The framework now pulls in extracted packages automatically
#    via "replace" declarations — no code changes needed

# 3. Optionally adopt package-style imports (recommended but not required)
#    Old: use App\Core\Auth\AuthManager;
#    New: use Zeroping\Auth\AuthManager;

# 4. Run the upgrade command
php zero upgrade:check
```

### 8.4 Namespace Migration Map

| Current (v2.0) | Package (v2.1+) | Package |
|----------------|-----------------|---------|
| `App\Core\Auth\*` | `Zeroping\Auth\*` | zeroping/auth |
| `App\Core\Mail\*` | `Zeroping\Mail\*` | zeroping/mail |
| `App\Core\Queue\*` | `Zeroping\Queue\*` | zeroping/queue |
| `App\Core\Notifications\*` | `Zeroping\Notifications\*` | zeroping/notifications |
| `App\Core\Filesystem\*` | `Zeroping\Storage\*` | zeroping/storage |
| `App\Core\Scheduling\*` | `Zeroping\Scheduler\*` | zeroping/scheduler |
| `App\Core\Localization\*` | `Zeroping\Localization\*` | zeroping/localization |
| `App\Core\Debug\*` | `Zeroping\Debug\*` | zeroping/debug |
| `App\Core\Testing\*` | `Zeroping\Testing\*` | zeroping/testing |
| `App\Core\Validation\*` | `Zeroping\Validation\*` | zeroping/framework |
| `App\Core\Cache\*` | `Zeroping\Cache\*` | zeroping/framework |
| `App\Core\Session\*` | `Zeroping\Session\*` | zeroping/framework |
| `App\Core\Container\*` | `Zeroping\Container\*` | zeroping/framework |
| `App\Core\Routing\*` | `Zeroping\Routing\*` | zeroping/framework |
| `App\Core\Database\*` | `Zeroping\Database\*` | zeroping/framework |
| `App\Core\Http\*` | `Zeroping\Http\*` | zeroping/framework |



---

## 9. Prioritized Implementation Order

### 9.1 Priority Definitions

| Priority | Meaning | Timeline |
|----------|---------|----------|
| **P0** | Must have. Blocks everything else. | Immediate (next 4 weeks) |
| **P1** | Critical for ecosystem launch. | Within current quarter |
| **P2** | Important for completeness. | Next quarter |
| **P3** | Nice to have. Future investment. | 6+ months |

### 9.2 P0 — Foundation (Weeks 1-4)

These items must be completed first. They unblock all other work.

| # | Task | Deliverable | Est. Effort |
|---|------|-------------|-------------|
| 1 | Finalize `zeroping/support` package | Working Composer package with ServiceProvider base, CommandRegistry, helpers | 3 days |
| 2 | Create `zeroping/framework` package | Extract Container, Router, Http, ORM, Validation, Cache, Session, Config, Events into package | 1 week |
| 3 | Extract `zeroping/auth` | Full auth package with guards, middleware, policies, make:auth | 4 days |
| 4 | Extract `zeroping/mail` | Mail package with drivers and Mailable classes | 2 days |
| 5 | Extract `zeroping/queue` | Queue package with sync/database drivers, worker, commands | 3 days |
| 6 | Set up mono-repo split workflow | GitHub Actions to split packages into read-only repos | 1 day |
| 7 | Update skeleton project | Default `create-project` uses package-based architecture | 2 days |
| 8 | Authentication starter kit | `zeroping/auth-kit` with login/register views and controllers | 3 days |
| 9 | Registry MVP | Basic Satis-based package registry at arena.zeroping.dev | 3 days |

**Total P0 estimate: ~4 weeks**

### 9.3 P1 — Ecosystem Launch (Weeks 5-12)

| # | Task | Deliverable | Est. Effort |
|---|------|-------------|-------------|
| 10 | Extract `zeroping/notifications` | Notifications package with mail/database channels | 3 days |
| 11 | Extract `zeroping/storage` | Storage package with local/S3 drivers | 3 days |
| 12 | Extract `zeroping/scheduler` | Scheduler package with cron, mutex, commands | 2 days |
| 13 | Extract `zeroping/testing` | Testing utilities, factories, fakes | 4 days |
| 14 | `php zero package:search` command | Search registry from CLI | 1 day |
| 15 | Package publishing flow | `php zero package:create` + publish to registry | 3 days |
| 16 | Arena website: package browser | Browse, search, and view packages on website | 1 week |
| 17 | Arena website: downloads page | Installation instructions and CLI download | 2 days |
| 18 | API documentation generator | OpenAPI spec from route annotations | 1 week |
| 19 | Hot-reload dev server | `php zero serve --watch` with file watcher | 3 days |
| 20 | Admin panel foundation | `zeroping/admin` with model CRUD and dashboard | 1 week |

**Total P1 estimate: ~8 weeks**

### 9.4 P2 — Completeness (Weeks 13-20)

| # | Task | Deliverable | Est. Effort |
|---|------|-------------|-------------|
| 21 | Extract `zeroping/localization` | i18n package | 2 days |
| 22 | Extract `zeroping/pagination` | Cursor + offset pagination | 2 days |
| 23 | Extract `zeroping/rate-limiter` | Standalone rate limiting | 2 days |
| 24 | Extract `zeroping/api-resources` | API transformers package | 2 days |
| 25 | Create `zeroping/search` | Full-text search with database driver | 1 week |
| 26 | Queue dashboard (web UI) | Visual queue monitoring in admin | 4 days |
| 27 | Scheduler dashboard (web UI) | Visual schedule monitoring in admin | 3 days |
| 28 | Community package submissions | Open registry to community packages | 1 week |
| 29 | Arena website: tutorials | Tutorial content and learning section | 1 week |
| 30 | Arena website: showcase | Community project showcase | 3 days |
| 31 | `php zero make:crud` | Full CRUD scaffolding command | 3 days |
| 32 | `php zero tinker` | Interactive REPL | 4 days |
| 33 | Release tooling | `php zero release` with changelog generation | 3 days |
| 34 | `zeroping/debug` standalone | Debug toolbar as installable package | 2 days |
| 35 | Websocket package | `zeroping/websocket` with broadcasting | 1 week |

**Total P2 estimate: ~8 weeks**

### 9.5 P3 — Platform (Weeks 21+)

| # | Task | Deliverable | Est. Effort |
|---|------|-------------|-------------|
| 36 | Arena Cloud MVP | Managed hosting platform | 3 months |
| 37 | Arena Forge | Server provisioning tool | 2 months |
| 38 | Redis drivers | Cache, session, queue on Redis | 1 week |
| 39 | `zeroping/tenancy` | Multi-tenancy package | 3 weeks |
| 40 | `zeroping/graphql` | GraphQL adapter | 2 weeks |
| 41 | Serverless adapter | AWS Lambda / Vercel deployment | 3 weeks |
| 42 | Arena Vapor | Serverless platform | 2 months |
| 43 | Arena Pulse | Monitoring and observability | 6 weeks |
| 44 | Arena Studio | Full web admin builder | 2 months |
| 45 | Distributed tracing | OpenTelemetry integration | 2 weeks |

### 9.6 Implementation Gantt (High-Level)

```
2026 Q3 (Jul-Sep)     2026 Q4 (Oct-Dec)     2027 Q1 (Jan-Mar)     2027 Q2+
│                     │                     │                     │
├── P0: Foundation ───┤                     │                     │
│   [support]         │                     │                     │
│   [framework]       │                     │                     │
│   [auth/mail/queue] │                     │                     │
│   [registry MVP]    │                     │                     │
│                     ├── P1: Ecosystem ────┤                     │
│                     │   [notifications]   │                     │
│                     │   [storage]         │                     │
│                     │   [scheduler]       │                     │
│                     │   [testing]         │                     │
│                     │   [admin panel]     │                     │
│                     │   [website expand]  │                     │
│                     │                     ├── P2: Complete ─────┤
│                     │                     │   [remaining pkgs]  │
│                     │                     │   [dashboards]      │
│                     │                     │   [community]       │
│                     │                     │   [websocket]       │
│                     │                     │                     ├── P3: Platform
│                     │                     │                     │   [Arena Cloud]
│                     │                     │                     │   [Arena Forge]
│                     │                     │                     │   [Arena Vapor]
│                     │                     │                     │   [Arena Pulse]
```

### 9.7 Success Metrics

| Milestone | Metric | Target |
|-----------|--------|--------|
| v2.1 release | Official packages published | 5+ packages on Packagist |
| v2.1 release | Registry operational | arena.zeroping.dev serving packages |
| v2.2 release | Developer adoption | 100+ downloads/month |
| v2.5 release | Community packages | 10+ community packages in registry |
| v3.0 release | Production deployments | Apps deployed on Arena Cloud |
| v3.0 release | Full package suite | 15 official packages published |
| v4.0 release | Platform revenue | Arena Cloud generating revenue |

### 9.8 Key Decisions Summary

| Decision | Rationale |
|----------|-----------|
| Mono-repo for development | Synchronized changes across packages, single CI, easier contributor experience |
| Read-only splits for distribution | Each package installable independently via Composer |
| Satis-based registry first | Low cost, fast to ship. Upgrade to custom registry later |
| Framework stays lightweight | Container + Router + ORM + Http + Validation + Cache + Session + Events = core |
| Everything else is opt-in | Auth, Mail, Queue, etc. are packages, not bundled in framework |
| Backward-compat aliases in v2.1 | Smooth upgrade path, no forced migration |
| Remove aliases in v3.0 | Clean break at major version, following semver |
| Admin panel as package, not product | Keeps it accessible (free, open-source). Studio is the paid product later |
| CLI remains the primary interface | Every package action available via `php zero <command>` |

---

## Appendix A: Package composer.json Template

```json
{
    "name": "zeroping/<package>",
    "description": "ZeroPing <Package> — <one-line description>",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "Rin Nairith",
            "email": "nairithrin143@gmail.com"
        }
    ],
    "require": {
        "php": ">=8.1",
        "zeroping/support": "^2.1"
    },
    "autoload": {
        "psr-4": {
            "Zeroping\\<Package>\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "Zeroping\\<Package>\\Tests\\": "tests/"
        }
    },
    "extra": {
        "zeroping": {
            "providers": [
                "Zeroping\\<Package>\\<Package>ServiceProvider"
            ]
        }
    },
    "minimum-stability": "stable"
}
```

## Appendix B: Arena Registry API (v1)

```
GET  /api/packages                    → List all packages (paginated)
GET  /api/packages/{vendor}/{name}    → Package details
GET  /api/packages/search?q=          → Search packages
POST /api/packages                    → Submit package (authenticated)
GET  /api/packages/{vendor}/{name}/stats → Download statistics
GET  /api/starters                    → List starter kits
GET  /api/categories                  → Package categories
```

## Appendix C: CLI Command Map (Full Ecosystem)

```
php zero new <app>                    # Create new project
php zero serve [--port] [--watch]     # Development server
php zero about                        # Framework info

# Packages
php zero package:list                 # List installed packages
php zero package:install <name>       # Install from registry
php zero package:remove <name>        # Remove package
php zero package:search <query>       # Search registry
php zero package:create <name>        # Scaffold new package
php zero package:publish [provider]   # Publish package assets
php zero vendor:publish               # Publish vendor assets

# Code Generation
php zero make:controller <name>       # Controller
php zero make:model <name>            # Model
php zero make:migration <name>        # Migration
php zero make:middleware <name>       # Middleware
php zero make:provider <name>         # Service provider
php zero make:request <name>          # Form request
php zero make:command <name>          # Console command
php zero make:test <name>             # Test class
php zero make:mail <name>             # Mailable
php zero make:notification <name>     # Notification
php zero make:event <name>            # Event
php zero make:listener <name>         # Listener
php zero make:job <name>              # Queue job
php zero make:factory <name>          # Model factory
php zero make:seeder <name>           # Database seeder
php zero make:policy <name>           # Authorization policy
php zero make:rule <name>             # Validation rule
php zero make:resource <name>         # API resource
php zero make:enum <name>             # Enum class
php zero make:exception <name>        # Exception class
php zero make:auth                    # Full auth scaffolding
php zero make:crud <model>            # Full CRUD scaffolding
php zero make:api <model>             # API resource scaffolding

# Database
php zero migrate                      # Run migrations
php zero migrate:rollback             # Rollback last batch
php zero migrate:fresh                # Drop all and re-migrate
php zero migrate:status               # Migration status
php zero db:seed                      # Run seeders

# Cache & Config
php zero config:cache                 # Cache configuration
php zero config:clear                 # Clear config cache
php zero route:cache                  # Cache routes
php zero route:clear                  # Clear route cache
php zero route:list                   # List all routes
php zero cache:clear                  # Clear app cache
php zero view:cache                   # Compile views
php zero view:clear                   # Clear view cache
php zero optimize                     # Cache everything
php zero optimize:clear               # Clear all caches

# Queue
php zero queue:work                   # Process jobs
php zero queue:listen                 # Listen for jobs
php zero queue:retry <id>             # Retry failed job
php zero queue:failed                 # List failed jobs
php zero queue:clear                  # Clear queue
php zero queue:restart                # Restart workers

# Scheduler
php zero schedule:run                 # Run due tasks
php zero schedule:list                # List scheduled tasks

# Security
php zero key:generate                 # Generate app key

# Testing
php zero test                         # Run test suite

# Deployment (future)
php zero deploy                       # Deploy to Arena Cloud
php zero cloud:login                  # Authenticate with Arena Cloud
php zero cloud:env                    # Manage cloud env vars
php zero cloud:logs                   # View cloud logs

# Documentation
php zero docs:generate                # Generate API docs
php zero docs:serve                   # Serve docs locally

# Release (future)
php zero release <version>            # Tag and release
php zero changelog:generate           # Generate changelog

# Diagnostics
php zero doctor                       # Environment check
php zero about                        # Framework info
php zero tinker                       # Interactive REPL
```

---

*This document is the single source of truth for the ZeroPing Arena ecosystem design.
All implementation work should reference this architecture.*

*Last updated: 2026-07-29 by Product Lead*
