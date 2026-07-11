# {{ project_name }}

> {{ project_description }}

## Requirements

- PHP 8.1+
- Composer
- MySQL-compatible database

## Installation

```bash
composer install
cp .env.example .env
php cli/migrate.php
php public/index.php
```

## Quick Start

```bash
php zero serve
```

Visit `http://localhost:1437` in your browser.

## Structure

```
{{ project_slug }}/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── ...
├── config/
├── database/
│   └── migrations/
├── public/
├── storage/
├── views/
├── .env.example
├── composer.json
└── README.md
```

## License

MIT
