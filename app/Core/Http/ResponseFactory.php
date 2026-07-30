<?php

declare(strict_types=1);

namespace App\Core\Http;

use App\Core\View\View;

/**
 * Fluent factory for building HTTP responses.
 *
 * Use the global response() helper: response()->json($data),
 * response()->view('home'), response()->redirect('/login'), etc.
 */
class ResponseFactory
{
    /**
     * Create a generic response with the given content.
     *
     * @param mixed                 $content Response body content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Response headers
     * @return Response
     */
    public function make(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * Create a JSON response.
     *
     * @param mixed                 $data    Data to be JSON-encoded
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Additional response headers
     * @return Response
     *
     * @throws \JsonException If JSON encoding fails
     */
    public function json(mixed $data, int $status = 200, array $headers = []): Response
    {
        return Response::json($data, $status)->withHeaders($headers);
    }

    /**
     * Create a response by rendering a view template.
     *
     * @param string                $view    The view template name
     * @param array<string, mixed>  $data    Data to pass to the view
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Additional response headers
     * @return Response
     */
    public function view(string $view, array $data = [], int $status = 200, array $headers = []): Response
    {
        $content = View::render($view, $data);

        $headers = array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers);

        return new Response($content, $status, $headers);
    }

    /**
     * Create an HTML response from a raw string.
     *
     * @param string                $content HTML content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Additional response headers
     * @return Response
     */
    public function html(string $content, int $status = 200, array $headers = []): Response
    {
        $headers = array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers);

        return new Response($content, $status, $headers);
    }

    /**
     * Create a plain-text response.
     *
     * @param string                $content Text content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Additional response headers
     * @return Response
     */
    public function text(string $content, int $status = 200, array $headers = []): Response
    {
        $headers = array_merge(['Content-Type' => 'text/plain; charset=utf-8'], $headers);

        return new Response($content, $status, $headers);
    }

    /**
     * Create a redirect response.
     *
     * @param string $to     The URL to redirect to
     * @param int    $status HTTP redirect status code (default 302)
     * @return Response
     */
    public function redirect(string $to, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $to]);
    }

    /**
     * Create an empty response with no content.
     *
     * @param int $status HTTP status code (default 204 No Content)
     * @return Response
     */
    public function noContent(int $status = 204): Response
    {
        return new Response('', $status);
    }

    /**
     * Create a file download response.
     *
     * @param string                $path    Absolute path to the file
     * @param string|null           $name    Download filename (defaults to basename)
     * @param array<string, string> $headers Additional response headers
     * @return Response
     *
     * @throws \InvalidArgumentException If the file does not exist
     */
    public function download(string $path, ?string $name = null, array $headers = []): Response
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("File not found: {$path}");
        }

        $name = $name ?? basename($path);
        $type = mime_content_type($path) ?: 'application/octet-stream';

        $headers = array_merge([
            'Content-Type'        => $type,
            'Content-Disposition' => 'attachment; filename="' . $name . '"',
        ], $headers);

        return new Response((string) file_get_contents($path), 200, $headers);
    }

    /**
     * Create a streamed file response (inline display, not download).
     *
     * @param string                $path    Absolute path to the file
     * @param array<string, string> $headers Additional response headers
     * @return Response
     *
     * @throws \InvalidArgumentException If the file does not exist
     */
    public function file(string $path, array $headers = []): Response
    {
        if (!is_file($path)) {
            throw new \InvalidArgumentException("File not found: {$path}");
        }

        $type = mime_content_type($path) ?: 'application/octet-stream';

        $headers = array_merge([
            'Content-Type'        => $type,
            'Content-Disposition' => 'inline',
        ], $headers);

        return new Response((string) file_get_contents($path), 200, $headers);
    }
}
