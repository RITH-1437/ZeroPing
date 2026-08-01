# Deployment

How to deploy a ZeroPing application to production, from environment configuration through to process management.

---

## Production checklist

Before going live, run through this list:

- [ ] `APP_ENV=production` in environment
- [ ] `APP_DEBUG=false` in environment
- [ ] `APP_KEY` is set (a strong random string)
- [ ] `.env` is **not** committed to version control and **not** accessible from the web
- [ ] `public/` is the only directory exposed to the web server
- [ ] `php zero config:cache` has been run to compile the config cache
- [ ] `php zero route:cache` has been run (if applicable)
- [ ] `composer install --no-dev --optimize-autoloader` (no dev dependencies)
- [ ] `storage/` is writable by the web server process
- [ ] Database migrations are up to date: `php zero migrate`
- [ ] Queue workers are running (if using queues)
- [ ] Scheduler cron entry is installed (if using the scheduler)
- [ ] SSL/TLS certificate is configured
- [ ] Error logging is directed to `storage/logs/` and monitored

---

## Environment configuration

Copy `.env.example` to `.env` and set all required values:

```ini
APP_NAME="My Application"
APP_ENV=production
APP_DEBUG=false
APP_KEY=your-random-secret-key-here
APP_URL=https://example.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myapp_production
DB_USERNAME=myapp
DB_PASSWORD=strong-database-password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=database

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=no-reply@example.com
MAIL_PASSWORD=smtp-password
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="My Application"
```

> In containerized environments, inject environment variables at the platform level (e.g., Docker `env_file`, Kubernetes `Secret`, AWS Parameter Store) rather than shipping a `.env` file inside the image.

---

## Optimization for production

### Config cache

Compiles all `config/*.php` files into `bootstrap/cache/config.php`. Eliminates file I/O for every config lookup:

```bash
php zero config:cache
```

When `APP_ENV=production`, ZeroPing loads the config cache **without mtime checking**, so this command must be re-run whenever config files change.

### Route cache

```bash
php zero route:cache
```

Compiles the route table into a cached file. Re-run after any change to `config/routes.php` or route files.

### Autoloader optimization

```bash
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

- `--no-dev` skips dev-only packages.
- `--optimize-autoloader` generates a class map for faster class resolution.
- `--classmap-authoritative` tells Composer only classes in the map exist — disables expensive filesystem fallbacks.

### OPcache

Enable and configure OPcache in PHP. The `.docker/php/opcache.ini` file in the repo is a production-ready starting point:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=0         ; never recheck in production
opcache.validate_timestamps=0     ; fastest: disable mtime checks
opcache.save_comments=1
```

When `validate_timestamps=0`, you must clear OPcache after each deployment: `opcache_reset()` or restart PHP-FPM.

---

## Docker deployment

The repository ships with a production-ready `Dockerfile` and `docker-compose.yml`.

### Building and running

```bash
# Build the image
docker build -t my-app:latest .

# Start services (app + nginx)
docker compose up -d
```

### How it works

The `Dockerfile` (PHP 8.3-FPM based):

1. Installs system dependencies and PHP extensions (`pdo_mysql`, `mbstring`, `gd`, etc.)
2. Copies application code into `/var/www`
3. Runs `composer install --no-dev --optimize-autoloader --classmap-authoritative`
4. Creates storage directories and sets correct ownership
5. Starts PHP's built-in server on port `1437` (configurable via `PORT` env var)

`docker-compose.yml` defines two services:

| Service | Image | Purpose |
|---|---|---|
| `app` (zeroping) | Custom build | Runs the PHP application |
| `web` (zeroping-web) | nginx:1.27-alpine | Terminates SSL, proxies to app |

Only `storage/` and `database/` are volume-mounted, keeping the application code immutable inside the container.

### Environment variables in Docker

```bash
# Pass via .env file (already configured in docker-compose.yml)
docker compose --env-file .env.production up -d

# Or override specific values
docker compose up -d -e APP_DEBUG=false -e APP_ENV=production
```

### Running migrations inside the container

```bash
docker exec zeroping php zero migrate
```

### Post-deployment steps

```bash
docker exec zeroping php zero config:cache
docker exec zeroping php zero route:cache
```

---

## nginx configuration

A minimal nginx config that passes all requests to `public/index.php`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;

    # Redirect HTTP to HTTPS
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name example.com www.example.com;

    ssl_certificate     /etc/letsencrypt/live/example.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem;

    root /var/www/public;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    # Static assets with far-future cache
    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Main entry point
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/run/php/php8.3-fpm.sock;  # or 127.0.0.1:9000
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    # Deny access to hidden files and sensitive directories
    location ~ /\. {
        deny all;
    }

    location ~ /(bootstrap|config|database|storage|app|vendor)/ {
        deny all;
    }
}
```

The `.docker/nginx/default.conf` in the repository uses the Docker service name `zeroping` as the upstream for `proxy_pass` instead of a Unix socket.

---

## Apache configuration

If deploying on Apache, ensure `mod_rewrite` is enabled and place an `.htaccess` in `public/`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [L]
</IfModule>

# Deny access to everything outside public/
<FilesMatch "^\.">
    Require all denied
</FilesMatch>
```

Set `DocumentRoot` to the `public/` directory in your VirtualHost:

```apache
<VirtualHost *:443>
    ServerName example.com
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

---

## Process management

### Queue workers

Queue workers are long-running PHP processes. Use a process manager to keep them running and restart them on failure.

#### Supervisor

Install Supervisor and create a config file at `/etc/supervisor/conf.d/zeroping-worker.conf`:

```ini
[program:zeroping-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/zero queue:work --tries=3 --sleep=3 --timeout=90
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/zeroping/worker.log
stopwaitsecs=120
```

Apply the config:

```bash
supervisorctl reread
supervisorctl update
supervisorctl start zeroping-worker:*
```

After each deployment, restart workers to pick up code changes:

```bash
supervisorctl restart zeroping-worker:*
```

#### Docker

Run a worker as a separate container:

```yaml
# docker-compose.yml
services:
  worker:
    build: .
    command: php zero queue:work --tries=3
    restart: unless-stopped
    env_file: .env
    volumes:
      - ./storage:/var/www/storage
      - ./database:/var/www/database
    depends_on:
      - app
```

### Scheduler

The scheduler requires a single cron entry that runs every minute. The framework checks internally which tasks are due:

```cron
* * * * * www-data php /var/www/html/zero schedule:run >> /dev/null 2>&1
```

Add this to `/etc/cron.d/zeroping` or to the `www-data` user's crontab.

In Docker, run the scheduler as a separate container:

```yaml
  scheduler:
    build: .
    command: sh -c "while true; do php zero schedule:run; sleep 60; done"
    restart: unless-stopped
    env_file: .env
    volumes:
      - ./storage:/var/www/storage
```

---

## Health checks

The `docker-compose.yml` includes a built-in health check against `http://localhost/up`. Create a dedicated health endpoint in your routes:

```php
// config/routes.php
Router::get('/up', function () {
    return response()->json(['status' => 'ok', 'timestamp' => time()]);
});
```

For a deeper health check that verifies the database connection:

```php
Router::get('/health', function () {
    try {
        \App\Core\Database\Database::connection()->getPdo()->query('SELECT 1');
        $db = 'ok';
    } catch (\Throwable) {
        $db = 'error';
    }

    $status = $db === 'ok' ? 200 : 503;

    return response()->json([
        'status'    => $db === 'ok' ? 'healthy' : 'degraded',
        'database'  => $db,
        'timestamp' => time(),
    ], $status);
});
```

Configure your load balancer or orchestration platform (Docker Swarm, Kubernetes) to poll this endpoint and only route traffic to healthy instances.

---

## Zero-downtime deployment

For production deployments with no dropped requests:

1. **Build the new image** (or deploy new code to a staging directory).
2. **Run migrations** before switching traffic: `php zero migrate`.
3. **Warm caches**: `php zero config:cache && php zero route:cache`.
4. **Swap traffic** (rolling restart in Kubernetes, swap upstream in nginx, or swap symlink in Capistrano-style deploys).
5. **Restart workers**: `supervisorctl restart zeroping-worker:*`.

When using rolling deployments, write migrations to be backward-compatible: add columns as nullable, drop columns in a separate migration after the new code is fully deployed.
