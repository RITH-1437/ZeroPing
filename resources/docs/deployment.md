# Deployment

ZeroPing requires **PHP 8.1+** with the following extensions enabled: `pdo`, `mbstring`, `json`, and a database driver (`pdo_sqlite`, `pdo_mysql`, or `pdo_pgsql`).

## Production .env settings

```bash
APP_ENV=production
APP_DEBUG=false
```

Set `APP_DEBUG=false` to disable pretty exception pages and suppress stack traces in production.

## Config caching

Merge all `config/*.php` files into a single cached file to reduce file I/O:

```bash
php zero config:cache
```

Clear the cache with `php zero config:clear`.

## Route caching

Compile all registered routes into an optimized file to skip parsing `config/routes.php` on every request:

```bash
php zero route:cache
```

Clear with `php zero route:clear`.

## Storage permissions

Ensure the web server user can write to the following directories:

- `storage/` — logs, compiled views, and framework caches.
- `bootstrap/cache/` — config and route cache files.

```bash
chmod -R 775 storage bootstrap/cache
```

## Database migration

Run pending migrations against your production database:

```bash
php zero migrate
```

## SQLite vs MySQL in production

SQLite is fine for low-traffic applications — just ensure the database file (`database/database.sqlite`) is writable by the web server. For higher traffic, switch to **MySQL** or **PostgreSQL** by updating `config/database.php`:

```php
config()->set('database.default', 'mysql');
```

```php
'connections' => [
    'mysql' => [
        'driver'    => 'mysql',
        'host'      => env('DB_HOST', '127.0.0.1'),
        'database'  => env('DB_DATABASE', 'zero_ping'),
        'username'  => env('DB_USERNAME', 'root'),
        'password'  => env('DB_PASSWORD', ''),
    ],
],
```

## Queues

Queue workers process queued jobs in the background. Use **Supervisor** (Linux) or **systemd** to keep the worker alive:

```bash
php zero queue:work
```

Example Supervisor configuration:

```ini
[program:zero-queue]
command=php /var/www/my-app/zero queue:work
autostart=true
autorestart=true
user=www-data
```

## Scheduler

Add a single cron entry to run scheduled tasks every minute:

```bash
* * * * * cd /path-to-project && php zero schedule:run >> /dev/null 2>&1
```

## Nginx configuration

```nginx
server {
    listen 80;
    server_name my-app.com;
    root /var/www/my-app/public;

    index router.php;

    location / {
        try_files $uri $uri/ /router.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

## Monitoring

Verify the installation and environment at any time:

```bash
php zero doctor
```

Monitor application health, queue depth, and response times:

```bash
php zero monitor
```
