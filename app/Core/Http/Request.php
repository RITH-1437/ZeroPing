<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\Support\Config;

/**
 * Core HTTP Request class.
 *
 * Provides both static helpers for quick access to the current request
 * data (via superglobals) and instance methods for a testable, DI-friendly
 * request object.
 *
 * @since 1.0.0
 * @author Rin Nairith
 */
class Request
{
    protected array $query;

    protected array $post;

    protected array $server;

    protected array $headers;

    protected array $files;

    /** @var array<string, mixed>|null Cached decoded JSON body */
    protected ?array $jsonBody = null;

    /**
     * Create a new Request instance.
     *
     * @param array $query  Query parameters ($_GET equivalent)
     * @param array $post   Post body parameters ($_POST equivalent)
     * @param array $server Server parameters ($_SERVER equivalent)
     * @param array $files  Uploaded files ($_FILES equivalent)
     */
    public function __construct(array $query = [], array $post = [], array $server = [], array $files = [])
    {
        $this->query = $query ?: $_GET;
        $this->post = $post ?: $_POST;
        $this->server = $server ?: $_SERVER;
        $this->files = $files ?: $_FILES;
        $this->headers = $this->parseHeaders($this->server);
    }

    /**
     * Capture the current HTTP request from superglobals.
     *
     * @return static
     */
    public static function capture(): static
    {
        return new static($_GET, $_POST, $_SERVER, $_FILES);
    }

    /**
     * Get the HTTP method, respecting the _method override in POST data.
     *
     * @return string Uppercase HTTP method (e.g. "GET", "POST", "PUT")
     */
    public static function method(): string
    {
        return strtoupper($_POST['_method'] ?? $_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get the full URL of the current request.
     *
     * @return string The complete URL including scheme, host, and URI
     */
    public static function url(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        if (!str_starts_with($uri, '/')) {
            $uri = '/';
        }

        return self::scheme() . '://' . self::host() . $uri;
    }

    /**
     * Get the path component of the current request URI.
     *
     * @return string The path without query string (e.g. "/users/1")
     */
    public static function path(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';
    }

    /**
     * Return a client address while trusting forwarding headers only when the
     * direct peer has explicitly been configured as a trusted proxy.
     *
     * @return string The client IP address
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

    /**
     * Determine if the current request path matches a given pattern.
     *
     * Supports wildcard (*) matching.
     *
     * @param string $pattern The URL pattern to match against (e.g. "admin/*")
     * @return bool True if the current path matches the pattern
     */
    public static function is(string $pattern): bool
    {
        $path = trim(static::path(), '/');
        $pattern = trim($pattern, '/');
        $regex = '#^' . str_replace('\\*', '.*', preg_quote($pattern, '#')) . '$#';

        return preg_match($regex, $path) === 1;
    }

    /**
     * Get a single input value from POST or GET data.
     *
     * @param string $key     The input key to retrieve
     * @param mixed  $default Default value if the key is not present
     * @return mixed The input value or default
     */
    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    /**
     * Get all input data merged from GET and POST.
     *
     * @return array<string, mixed> All input parameters
     */
    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    /**
     * Get a request header value.
     *
     * @param string      $key     The header name (e.g. "Content-Type", "Authorization")
     * @param string|null $default Default value if the header is not present
     * @return string|null The header value or default
     */
    public static function header(string $key, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));

        return $_SERVER[$normalized] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Determine if a given input key exists in POST or GET data.
     *
     * @param string $key The input key to check
     * @return bool True if the key exists
     */
    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    /**
     * Get a subset of the input data by the given keys.
     *
     * @param array<int, string> $keys The keys to include
     * @return array<string, mixed> Filtered input data
     */
    public static function only(array $keys): array
    {
        return array_intersect_key(static::all(), array_flip($keys));
    }

    /**
     * Get all input data except for the given keys.
     *
     * @param array<int, string> $keys The keys to exclude
     * @return array<string, mixed> Filtered input data
     */
    public static function except(array $keys): array
    {
        return array_diff_key(static::all(), array_flip($keys));
    }

    /**
     * Determine if the request has a JSON content type.
     *
     * @return bool True if Content-Type contains "application/json"
     */
    public static function isJson(): bool
    {
        return str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    }

    /**
     * Get decoded JSON from the request body.
     *
     * When called without a key, returns the entire decoded array.
     * When called with a key, returns that specific value.
     *
     * @param string|null $key     Optional key to retrieve from the JSON body
     * @param mixed       $default Default value if the key is not present
     * @return mixed The decoded JSON data (array), a specific value, or default
     */
    public static function json(?string $key = null, mixed $default = null): mixed
    {
        $data = json_decode((string) file_get_contents('php://input'), true) ?? [];

        return $key === null ? $data : ($data[$key] ?? $default);
    }

    /**
     * Extract the bearer token from the Authorization header.
     *
     * @return string|null The token string, or null if not present
     */
    public static function bearerToken(): ?string
    {
        $header = static::header('Authorization');
        if ($header !== null && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Determine what content type the client expects in the response.
     *
     * @param string $contentType The content type to check (e.g. "application/json")
     * @return bool True if the Accept header contains the given content type
     */
    public static function expects(string $contentType): bool
    {
        $accept = static::header('Accept', '') ?? '';

        return str_contains($accept, $contentType);
    }

    /**
     * Determine if the client expects a JSON response.
     *
     * @return bool True if the client wants JSON
     */
    public static function wantsJson(): bool
    {
        $accept = static::header('Accept', '') ?? '';

        if (str_contains($accept, 'application/json')) {
            return true;
        }

        if (static::isJson() && !str_contains($accept, 'text/html')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the current request is an AJAX/XHR request.
     *
     * @return bool True if X-Requested-With is XMLHttpRequest
     */
    public static function isAjax(): bool
    {
        return static::header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Get an input value from the instance's post/query data.
     *
     * @param string $key     The input key
     * @param mixed  $default Default value if the key is not present
     * @return mixed The value or default
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Get a header value from the instance's parsed headers.
     *
     * @param string      $key     The header name (lowercase)
     * @param string|null $default Default value if the header is not present
     * @return string|null The header value or default
     */
    public function getHeader(string $key, ?string $default = null): ?string
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    /**
     * Get the HTTP method from the instance's server data.
     *
     * @return string Uppercase HTTP method
     */
    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Get all parsed headers from the instance.
     *
     * @return array<string, string> Headers keyed by lowercase name
     */
    public function getAllHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get the bearer token from the instance's Authorization header.
     *
     * @return string|null The token string, or null if not present
     */
    public function getBearerToken(): ?string
    {
        $header = $this->getHeader('authorization');
        if ($header !== null && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        return null;
    }

    /**
     * Get decoded JSON body from the instance.
     *
     * @param string|null $key     Optional key to retrieve
     * @param mixed       $default Default value if key is not present
     * @return mixed Decoded JSON data or specific value
     */
    public function getJson(?string $key = null, mixed $default = null): mixed
    {
        if ($this->jsonBody === null) {
            $raw = (string) file_get_contents('php://input');
            $this->jsonBody = json_decode($raw, true) ?? [];
        }

        return $key === null ? $this->jsonBody : ($this->jsonBody[$key] ?? $default);
    }

    /**
     * Determine the request scheme (http or https).
     *
     * @return string "http" or "https"
     */
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

    /**
     * Determine the request host, sanitizing untrusted input.
     *
     * @return string The validated hostname or "localhost"
     */
    private static function host(): string
    {
        $host = trim((string) ($_SERVER['HTTP_HOST'] ?? 'localhost'));
        if (preg_match('/^[A-Za-z0-9.-]+(?::[0-9]{1,5})?$/D', $host) !== 1) {
            return 'localhost';
        }

        return $host;
    }

    /**
     * Check if the given address is in the list of trusted proxies.
     *
     * @param string $address The IP address to check
     * @return bool True if the address is trusted
     */
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

    /**
     * Parse HTTP headers from server parameters.
     *
     * @param array $server The server parameters array
     * @return array<string, string> Headers keyed by lowercase name
     */
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
