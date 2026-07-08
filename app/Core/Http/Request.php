<?php

namespace App\Core\Http;

class Request
{
    protected array $query;
    protected array $post;
    protected array $server;
    protected array $headers;
    protected array $files;

    public function __construct(
        array $query = [],
        array $post = [],
        array $server = [],
        array $files = []
    ) {
        $this->query   = $query  ?: $_GET;
        $this->post    = $post   ?: $_POST;
        $this->server  = $server ?: $_SERVER;
        $this->files   = $files  ?: $_FILES;
        $this->headers = $this->parseHeaders($this->server);
    }

    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    // ── Static proxy helpers ─────────────────────────────────────────────────

    public static function method(): string
    {
        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET';
        return strtoupper($method);
    }

    public static function url(): string
    {
        $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri      = $_SERVER['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }

    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    public static function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '127.0.0.1';
    }

    public static function is(string $pattern): bool
    {
        $path    = trim(static::path(), '/');
        $pattern = trim($pattern, '/');

        $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
        return (bool) preg_match($regex, $path);
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
        $all = static::all();
        return array_intersect_key($all, array_flip($keys));
    }

    public static function except(array $keys): array
    {
        return array_diff_key(static::all(), array_flip($keys));
    }

    public static function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    public static function json(?string $key = null, mixed $default = null): mixed
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    // ── Instance helpers ──────────────────────────────────────────────────────

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    public function getHeader(string $key, ?string $default = null): ?string
    {
        $normalized = strtolower($key);
        return $this->headers[$normalized] ?? $default;
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private function parseHeaders(array $server): array
    {
        $headers = [];
        foreach ($server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name           = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }
}
