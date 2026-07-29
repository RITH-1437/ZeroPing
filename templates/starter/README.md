# {{PROJECT_NAME}}

{{ project_description }}

A ZeroPing application generated from the Starter template.

## Requirements

- PHP {{PHP_VERSION}} or newer
- Composer

## Get started

```bash
composer install
cp .env.example .env
php zero key:generate
php zero migrate
php zero serve
```

Open `http://localhost:1437` in your browser.

## Project structure

- `app/` — controllers, models, middleware, services, and sample application code
- `config/` — application and infrastructure configuration
- `database/` — migrations and seeders
- `views/` — server-rendered templates
- `tests/` — PHPUnit tests

## Documentation

- [Introduction](https://zero-ping.duckdns.org/docs/introduction)
- [Routing](https://zero-ping.duckdns.org/docs/routing)
- [Middleware](https://zero-ping.duckdns.org/docs/middleware)
- [Database](https://zero-ping.duckdns.org/docs/database)
- [Packages](https://zero-ping.duckdns.org/docs/packages)

## Support

See the [ZeroPing repository](https://github.com/RITH-1437/ZeroPing) for issues, discussions, and releases.
