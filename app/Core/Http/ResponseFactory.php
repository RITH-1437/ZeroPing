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
     * @param mixed $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function make(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }

    /**
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function json(mixed $data, int $status = 200, array $headers = []): Response
    {
        return Response::json($data, $status)->withHeaders($headers);
    }

    /**
     * @param string $view
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function view(string $view, array $data = [], int $status = 200, array $headers = []): Response
    {
        $content = View::render($view, $data);

        $headers = array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers);

        return new Response($content, $status, $headers);
    }

    /**
     * @param string $content
     * @param int $status
     * @param array $headers
     * @return Response
     */
    public function html(string $content, int $status = 200, array $headers = []): Response
    {
        $headers = array_merge(['Content-Type' => 'text/html; charset=utf-8'], $headers);

        return new Response($content, $status, $headers);
    }

    /**
     * @param string $to
     * @param int $status
     * @return Response
     */
    public function redirect(string $to, int $status = 302): Response
    {
        return new Response('', $status, ['Location' => $to]);
    }

    /**
     * @param int $status
     * @return Response
     */
    public function noContent(int $status = 204): Response
    {
        return new Response('', $status);
    }

    /**
     * @param string $path
     * @param string|null $name
     * @param array $headers
     * @return Response
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
}
