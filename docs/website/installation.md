# Installation

Install ZeroPing with Composer and run migrations.

## Requirements

- PHP 8.1+
- Composer
- MySQL-compatible database

## Clone the repository

```bash
git clone https://github.com/RITH-1437/ZeroPing.git
cd ZeroPing
```

## Install dependencies

```bash
composer install
```

## Environment configuration

```bash
cp .env.example .env
```

Update your database credentials in `.env`, then run:

```bash
php cli\migrate.php
```

## Serve the application

```bash
php -S localhost:1437 -t public
```
