<p align="center">
  <a href="https://zero-ping.duckdns.org">
    <img src="https://raw.githubusercontent.com/rith-1437/ZeroPing/main/public/assets/images/mascot.svg" alt="ZeroPing" width="120">
  </a>
</p>

<h1 align="center">ZeroPing</h1>

<p align="center">
  <strong>Clean. Expressive. Familiar.</strong><br>
  A modern PHP framework built from scratch.
</p>

<p align="center">
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/v/rith-1437/zeroping.svg?style=flat-square&color=14B8A6" alt="Latest Stable Version"></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg?style=flat-square" alt="PHP >= 8.1">
  <a href="https://github.com/rith-1437/ZeroPing/blob/main/LICENSE"><img src="https://img.shields.io/github/license/rith-1437/ZeroPing?style=flat-square" alt="License"></a>
  <a href="https://github.com/rith-1437/ZeroPing/actions"><img src="https://img.shields.io/github/actions/workflow/status/rith-1437/ZeroPing/ci.yml?style=flat-square" alt="Tests"></a>
  <a href="https://packagist.org/packages/rith-1437/zeroping"><img src="https://img.shields.io/packagist/dt/rith-1437/zeroping.svg?style=flat-square" alt="Downloads"></a>
</p>

---

## Introduction

ZeroPing is a lightweight, modern PHP framework built from scratch with a clean MVC architecture, a fast dependency-injection container, an expressive ORM, validation, caching, a background queue, a task scheduler, and developer-friendly CLI tooling.

It is free, open-source, and created by **Rin Nairith**.

## Features

| Category | Features |
|----------|----------|
| **Architecture** | MVC, Dependency Injection, Service Providers, Middleware Pipeline |
| **Routing** | Static & Dynamic Routes, Route Groups, Prefixes, Named Routes |
| **Database** | ORM with Relationships, Query Builder, Migrations, Seeding |
| **Validation** | Fluent Validator, FormRequest, 20+ Built-in Rules |
| **Security** | CSRF Protection, Encryption, Rate Limiting, Hashing |
| **Performance** | File/Array/Database Cache, Route Caching, Config Caching |
| **Background** | Queue with Sync/Database Drivers, Task Scheduler |
| **Developer Experience** | CLI Tooling, Starter Templates, Debug Toolbar, Logging |
| **Testing** | PHPUnit Integration, HTTP Assertions, Database Transactions |

## Installation

**Zero CLI** (recommended):

```bash
php zero new my-app
cd my-app
php zero serve
```

**Composer**:

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
php zero serve
```

Open [http://localhost:1437](http://localhost:1437).

## Quick Start

**1. Define a route** (`config/routes.php`):

```php
use App\Core\Routing\Router;
use App\Controllers\GreetingController;

Router::get('/hello', [GreetingController::class, 'index']);
```

**2. Create a controller** (`app/Controllers/GreetingController.php`):

```php
<?php

namespace App\Controllers;

use App\Core\View\Controller;

class GreetingController extends Controller
{
    public function index(): string
    {
        return view('greeting', [
            'name' => config('app.name'),
        ]);
    }
}
```

**3. Create a view** (`views/greeting.php`):

```php
<h1>Hello from <?= e($name) ?>!</h1>
```

Start the server and visit your page:

```bash
php zero serve
# open http://localhost:1437/hello
```

## Documentation

Full documentation is available at [zero-ping.duckdns.org](https://zero-ping.duckdns.org):

| Page | Description |
|------|-------------|
| [Introduction](https://zero-ping.duckdns.org/docs/introduction) | What ZeroPing is and why it exists |
| [Installation](https://zero-ping.duckdns.org/installation) | Install and configure ZeroPing |
| [Getting Started](https://zero-ping.duckdns.org/getting-started) | Build your first app |
| [CLI Reference](https://zero-ping.duckdns.org/docs/cli) | Complete CLI command reference |
| [Database & ORM](https://zero-ping.duckdns.org/docs/database) | Models, relationships, migrations |
| [Validation](https://zero-ping.duckdns.org/docs/validation) | Rules, FluentValidator, FormRequest |
| [Caching](https://zero-ping.duckdns.org/docs/caching) | File, array, and database cache |
| [Queues](https://zero-ping.duckdns.org/docs/queues) | Background job processing |
| [Scheduler](https://zero-ping.duckdns.org/docs/scheduler) | Task scheduling |
| [Security](https://zero-ping.duckdns.org/docs/security) | Encryption, hashing, CSRF |

## Contributing

Thank you for considering contributing to ZeroPing! Please read the [contribution guide](CONTRIBUTING.md).

## License

ZeroPing is open-source software licensed under the [MIT license](LICENSE).
