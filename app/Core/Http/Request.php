<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Support\Config;

class Request
{
    protected array $query;
    protected array $post;
    protected array $server;
    protected array $headers;
    protected array $files;

    public function __construct(array $query = [], array $post = [], array $server = [], array $files = [])
    {
        $this->query = $query ?: $_GET;
        $this->post = $post ?: $_POST;
        $this->server = $server ?: $_SERVER;
        $this->files = $files ?: $_FILES;
        $this->headers = $this->parseHeaders($this->server);
    }

    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    public static function method(): string
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public static function url(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (!str_starts_with($uri, '/')) {
            $uri = '/';
        }

        return self::scheme() . '://' . self::host() . $uri;
    }

    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    /**
     * Return a client address while trusting forwarding headers only when the
     * direct peer has explicitly been configured as a trusted proxy.
     */
    public static function ip(): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        if (!filter_var($remote, FILTER_VALIDATE_IP) || !self::isTrustedProxy($remote)) {
            return $remote;
        }

        foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '') as $candidate) {
            $candidate = trim($candidate);
            if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                return $candidate;
            }
        }

        return $remote;
    }

    public static function is(string $pattern): bool
    {
        $path = trim(static::path(), '/');
        $pattern = trim($pattern, '/');
        $regex = '#^' . str_replace('\\*', '.*', preg_quote($pattern, '#')) . '$#';

        return preg_match($regex, $path) === 1;
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function header(string $key, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$normalized] ?? $_SERVER[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function only(array $keys): array
    {
        return array_intersect_key(static::all(), array_flip($keys));
    }

    public static function except(array $keys): array
    {
        return array_diff_key(static::all(), array_flip($keys));
    }

    public static function isJson(): bool
    {
        return str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }

    public static function json(?string $key = null, mixed $default = null): mixed
    {
        $data = json_decode((string) file_get_contents('php://input'), true) ?? [];
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function getHeader(string $key, ?string $default = null): ?string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    private static function scheme(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';

        if (self::isTrustedProxy($remote)) {
            $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '');
            $forwarded = strtolower(trim($parts[0]));
            if (in_array($forwarded, ['http', 'https'], true)) {
                $scheme = $forwarded;
            }
        }

        return $scheme;
    }

    private static function host(): string
    {
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        if (preg_match('/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/D', $host) !== 1) {
            return 'localhost';
        }

        return $host;
    }

    private static function isTrustedProxy(string $address): bool
    {
        try {
            $proxies = Config::get('security.trusted_proxies', []);
        } catch (\Throwable) {
            return false;
        }

        if (is_string($proxies)) {
            $proxies = array_filter(array_map('trim', explode(',', $proxies)));
        }

        return is_array($proxies) && in_array($address, $proxies, true);
    }

    private function parseHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headers[strtolower(str_replace('_', '-', substr($key, 5)))] = $value;
            }
        }

        return $headers;
    }
}
