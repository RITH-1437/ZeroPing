<?php

namespace App\Core\Http;

class Request
{
    protected array $query;
    protected array $post;
    protected array $server;
    protected array $headers;
    protected array $files;

    /**
     * @param array $query  GET parameters
     * @param array $post   POST parameters
     * @param array $server Server parameters
     * @param array $files  Uploaded files
     */
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

    /**
     * Create a new request from the current HTTP globals.
     */
    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    // ── Static proxy helpers ─────────────────────────────────────────────────

    /**
     * Get the HTTP method of the current request.
     */
    public static function method(): string
    {
        $method = $_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET';
        return strtoupper($method);
    }

    /**
     * Get the full URL of the current request.
     */
    public static function url(): string
    {
        $scheme   = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $uri      = $_SERVER['REQUEST_URI'] ?? '/';
        return $scheme . '://' . $host . $uri;
    }

    /**
     * Get the URI path of the current request.
     */
    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    /**
     * Get the client IP address.
     */
    public static function ip(): string
    {
        return $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '127.0.0.1';
    }

    /**
     * Determine if the current request URI matches a pattern.
     *
     * @param string $pattern The pattern to match
     * @return bool
     */
    public static function is(string $pattern): bool
    {
        $path    = trim(static::path(), '/');
        $pattern = trim($pattern, '/');

        $regex = '#^' . str_replace('\*', '.*', preg_quote($pattern, '#')) . '$#';
        return (bool) preg_match($regex, $path);
    }

    /**
     * Retrieve an input value from the request.
     *
     * @param string $key     The input key
     * @param mixed  $default The default value
     * @return mixed
     */
    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get all input data from the request.
     *
     * @return array
     */
    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Retrieve a header value from the request.
     *
     * @param string      $key     The header name
     * @param string|null $default The default value
     * @return string|null
     */
    public static function header(string $key, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$normalized] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Determine if the request has a given input key.
     *
     * @param string $key The input key
     * @return bool
     */
    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Get a subset of input values.
     *
     * @param array $keys The keys to retrieve
     * @return array
     */
    public static function only(array $keys): array
    {
        $all = static::all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get all input values except the given keys.
     *
     * @param array $keys The keys to exclude
     * @return array
     */
    public static function except(array $keys): array
    {
        return array_diff_key(static::all(), array_flip($keys));
    }

    /**
     * Determine if the request expects JSON.
     *
     * @return bool
     */
    public static function isJson(): bool
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        return str_contains($contentType, 'application/json');
    }

    /**
     * Retrieve JSON input from the request body.
     *
     * @param string|null $key     The JSON key
     * @param mixed       $default The default value
     * @return mixed
     */
    public static function json(?string $key = null, mixed $default = null): mixed
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        return $key === null ? $data : ($data[$key] ?? $default);
    }

    // ── Instance helpers ──────────────────────────────────────────────────────

    /**
     * Retrieve a value from the request input.
     *
     * @param string $key     The input key
     * @param mixed  $default The default value
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Retrieve a header value from the request.
     *
     * @param string      $key     The header name
     * @param string|null $default The default value
     * @return string|null
     */
    public function getHeader(string $key, ?string $default = null): ?string
    {
        $normalized = strtolower($key);
        return $this->headers[$normalized] ?? $default;
    }

    /**
     * Get the HTTP method from the server parameters.
     *
     * @return string
     */
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
