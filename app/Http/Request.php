<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Filesystem\UploadedFile;

/**
 * Application-level HTTP Request.
 *
 * Extends the Core Request with convenience static methods for quick
 * access to superglobal request data. All methods are backward-compatible
 * with existing usage throughout the application.
 */
class Request extends \App\Core\Http\Request
{
    /**
     * Capture the current HTTP request from superglobals.
     *
     * @return static
     */
    public static function capture(): static
    {
        return new static();
    }

    /**
     * Get the raw HTTP request method (without _method override).
     *
     * @return string Uppercase HTTP method
     */
    public static function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Determine if the request method is GET.
     *
     * @return bool True if the method is GET
     */
    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    /**
     * Determine if the request method is POST.
     *
     * @return bool True if the method is POST
     */
    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    /**
     * Determine if the request method is PUT.
     *
     * @return bool True if the method is PUT
     */
    public static function isPut(): bool
    {
        return self::method() === 'PUT';
    }

    /**
     * Determine if the request method is DELETE.
     *
     * @return bool True if the method is DELETE
     */
    public static function isDelete(): bool
    {
        return self::method() === 'DELETE';
    }

    /**
     * Determine if the request method is PATCH.
     *
     * @return bool True if the method is PATCH
     */
    public static function isPatch(): bool
    {
        return self::method() === 'PATCH';
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
     * Get an uploaded file by key.
     *
     * @param string $key The file input name
     * @return UploadedFile|null The uploaded file instance, or null if not present
     */
    public static function file(string $key): ?UploadedFile
    {
        if (isset($_FILES[$key])) {
            return new UploadedFile($_FILES[$key]);
        }

        return null;
    }

    /**
     * Determine if a file was uploaded with the given key.
     *
     * @param string $key The file input name
     * @return bool True if the file exists in the upload
     */
    public static function hasFile(string $key): bool
    {
        return isset($_FILES[$key]) && ($_FILES[$key]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Get all uploaded files.
     *
     * @return array<string, UploadedFile> Array of UploadedFile instances keyed by input name
     */
    public static function files(): array
    {
        $files = [];

        foreach ($_FILES as $key => $file) {
            $files[$key] = new UploadedFile($file);
        }

        return $files;
    }

    /**
     * Determine if the current request path matches a given pattern.
     *
     * Uses fnmatch for pattern matching.
     *
     * @param string $pattern The URL pattern (e.g. "admin/*")
     * @return bool True if the current path matches
     */
    public static function is(string $pattern): bool
    {
        $path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/');

        return fnmatch($pattern, $path);
    }

    /**
     * Get a request header value.
     *
     * @param string      $key     The header name (e.g. "Content-Type")
     * @param string|null $default Default value if the header is not present
     * @return string|null The header value or default
     */
    public static function header(string $key, ?string $default = null): ?string
    {
        $normalized = 'HTTP_' . strtoupper(str_replace('-', '_', $key));

        return $_SERVER[$normalized] ?? $default;
    }

    /**
     * Get the full URL of the current request.
     *
     * @return string The complete URL including scheme, host, and URI
     */
    public static function url(): string
    {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
            ? 'https'
            : 'http';

        return $scheme . "://" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/');
    }

    /**
     * Get the client's IP address.
     *
     * @return string The remote IP address
     */
    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Get the query string from the request.
     *
     * @return string The query string without the leading "?"
     */
    public static function queryString(): string
    {
        return $_SERVER['QUERY_STRING'] ?? '';
    }

    /**
     * Get the user agent string.
     *
     * @return string|null The User-Agent header value
     */
    public static function userAgent(): ?string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Determine if the request was made over HTTPS.
     *
     * @return bool True if the connection is secure
     */
    public static function isSecure(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }
}
