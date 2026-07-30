<?php

declare(strict_types=1);

namespace App\Http;

use App\Core\Http\Response as CoreResponse;

/**
 * Static response helper for quick HTTP responses.
 *
 * This class provides simple static methods for sending redirects and JSON
 * responses. For a fully object-oriented, testable response builder, use
 * App\Core\Http\Response or the response() helper instead.
 */
class Response
{
    /**
     * Redirect the client to a given URL.
     *
     * Does not call exit() — the caller is expected to return or stop
     * further processing if needed.
     *
     * @param string $url    The URL to redirect to
     * @param int    $status HTTP redirect status code (default 302)
     * @return void
     */
    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
    }

    /**
     * Send a JSON response.
     *
     * Does not call exit() — the caller is expected to return or stop
     * further processing if needed.
     *
     * @param mixed $data    Data to be JSON-encoded
     * @param int   $status  HTTP status code
     * @param int   $options JSON encoding options (default: 0)
     * @return void
     */
    public static function json(mixed $data, int $status = 200, int $options = 0): void
    {
        http_response_code($status);
        header('Content-Type: application/json');

        echo json_encode($data, $options);
    }

    /**
     * Send a plain-text response.
     *
     * @param string $content The text content
     * @param int    $status  HTTP status code
     * @return void
     */
    public static function text(string $content, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/plain; charset=utf-8');

        echo $content;
    }

    /**
     * Send an HTML response.
     *
     * @param string $content The HTML content
     * @param int    $status  HTTP status code
     * @return void
     */
    public static function html(string $content, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: text/html; charset=utf-8');

        echo $content;
    }

    /**
     * Send a "No Content" response (204).
     *
     * @return void
     */
    public static function noContent(): void
    {
        http_response_code(204);
    }

    /**
     * Create a fluent CoreResponse instance for advanced building.
     *
     * Usage: Response::make('body')->withStatus(201)->withHeader('X-Custom', 'value')->send();
     *
     * @param mixed                 $content Response body content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Response headers
     * @return CoreResponse
     */
    public static function make(mixed $content = '', int $status = 200, array $headers = []): CoreResponse
    {
        return new CoreResponse($content, $status, $headers);
    }
}
