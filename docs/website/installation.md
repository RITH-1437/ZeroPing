# Installation

ZeroPing is distributed as a Composer package. The recommended way to start a
new project is `composer create-project`, which installs the framework, copies
your environment file, generates an application key, and prepares storage.

## Requirements

- PHP 8.1 or higher
- The following PHP extensions: `pdo`, `pdo_mysql`, `mbstring`, `json`,
  `ctype`, `tokenizer`, `fileinfo`, `openssl`, `hash`
- Composer
- A MySQL-compatible database (or any PDO-supported engine)

## Create a new project

```bash
composer create-project rith-1437/zeroping my-app
cd my-app
```

The `post-create-project-cmd` script runs automatically and will:

1. Create `storage/cache`, `storage/logs`, and `bootstrap/cache`.
2. Copy `.env.example` to `.env`.
3. Generate a random `APP_KEY`.

## Alternative: install from source

If you are contributing to the framework, clone the repository instead:

```bash
git clone https://github.com/RITH-1437/zero-ping.git
cd zero-ping
composer install
cp .env.example .env
php zero key:generate
```

## Configure the environment

Open `.env` and set your database credentials:

```dotenv
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=zeroping
DB_USER=root
DB_PASS=
```

## Run migrations

```bash
php zero migrate
```

## Start the development server

```bash
php zero serve
```

By default the server listens on `http://localhost:1437`. Pass a port to
override: `php zero serve 9000`.

## Verify your installation

Run the built-in doctor to confirm PHP, extensions, environment, and database
connectivity are correctly configured:

```bash
php zero doctor
```
