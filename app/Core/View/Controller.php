<?php

declare(strict_types=1);

namespace App\Core\View;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Security\CSRF;
use App\Core\Session\Flash;

/**
 * Base controller class.
 *
 * Provides common utilities for rendering views, validating CSRF tokens,
 * performing redirects, and returning JSON responses.
 */
class Controller
{
    /**
     * Render a view template with optional layout.
     *
     * @param string      $view   The view template name
     * @param array       $data   Data to pass to the view
     * @param string|null $layout The layout wrapper (null for no layout)
     * @return string The rendered HTML content
     */
    protected function view(
        string $view,
        array $data = [],
        ?string $layout = 'guest'
    ): string {
        return View::render($view, $data, $layout);
    }

    /**
     * Validate the CSRF token from the current request.
     *
     * Checks the _token input value against the session token.
     * Flashes an error message on failure.
     *
     * @return bool True if the token is valid, false otherwise
     */
    protected function validateCsrf(): bool
    {
        $token = Request::input('_token');
        if (!$token || !CSRF::check($token)) {
            Flash::error('Invalid security token. Please try again.');
            return false;
        }

        return true;
    }

    /**
     * Redirect the client to a given URL.
     *
     * @param string $url The URL to redirect to
     * @return never This method terminates execution
     */
    protected function redirect(string $url): never
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            $url = '/';
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect the client back to the previous page.
     *
     * Uses the HTTP Referer header if available and from the same host.
     * Falls back to the given fallback URL.
     *
     * @param string $fallback Fallback URL if referer is unavailable or untrusted
     * @return never This method terminates execution
     */
    protected function redirectBack(string $fallback = '/'): never
    {
        $url = $fallback;
        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            $parsed = parse_url($referer);
            if ($parsed !== false) {
                $host = $parsed['host'] ?? '';
                $expected = parse_url(($_ENV['APP_URL'] ?? 'http://localhost'), PHP_URL_HOST) ?? 'localhost';
                if ($host === $expected || $host === '') {
                    $url = $referer;
                }
            }
        }
        header('Location: ' . $url);
        exit;
    }

    /**
     * Return a JSON response using the Core Response object.
     *
     * @param mixed $data    Data to encode as JSON
     * @param int   $status  HTTP status code
     * @return Response
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }

    /**
     * Return a response object for further customization.
     *
     * @param mixed                 $content Response body content
     * @param int                   $status  HTTP status code
     * @param array<string, string> $headers Response headers
     * @return Response
     */
    protected function response(mixed $content = '', int $status = 200, array $headers = []): Response
    {
        return new Response($content, $status, $headers);
    }
}
