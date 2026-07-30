<?php

declare(strict_types=1);

namespace App\Core\Http;

/**
 * Renders HTTP error pages.
 *
 * Extracted from the Router to separate presentation concerns from
 * routing logic. Looks for error views under views/errors/{code}.php
 * and falls back to a plain-text response when no view is available.
 */
class ErrorRenderer
{
    /**
     * Render an HTTP error page and set the appropriate status code.
     *
     * The method looks for a dedicated view at {frameworkPath}/views/errors/{code}.php.
     * If that file does not exist it falls back to the generic 500 view. When no view
     * file exists at all, a minimal plain-text response is sent so the error never
     * itself triggers a fatal.
     *
     * @param string         $frameworkPath  Root path of the framework (where views/ lives).
     * @param int            $code           HTTP status code (e.g. 404, 500).
     * @param \Throwable|null $e             The exception that caused the error, or null.
     */
    public static function render(string $frameworkPath, int $code, ?\Throwable $e): void
    {
        http_response_code($code);

        $message       = $e !== null ? $e->getMessage() : '';
        $exception     = $e !== null ? get_class($e) : '';
        $file          = $e !== null ? $e->getFile() : '';
        $line          = $e !== null ? $e->getLine() : 0;
        $trace         = $e !== null ? $e->getTrace() : [];
        $requestUrl    = $_SERVER['REQUEST_URI'] ?? '/';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $environment   = $_ENV['APP_ENV'] ?? 'local';
        $debug         = function_exists('config') ? (bool) config('app.debug', false) : false;
        $active        = '';

        $view = $frameworkPath . '/views/errors/' . $code . '.php';

        if (!file_exists($view)) {
            $view = $frameworkPath . '/views/errors/500.php';
        }

        if (file_exists($view)) {
            require $view;
            return;
        }

        // Last-resort fallback so an error never itself fatals.
        echo htmlspecialchars((string) $code, ENT_QUOTES) . ' Error';
    }

    /**
     * Determine the appropriate HTTP status code from an exception.
     *
     * Maps known exception codes to valid HTTP codes; anything else becomes 500.
     *
     * @param \Throwable $e
     * @return int
     */
    public static function resolveHttpCode(\Throwable $e): int
    {
        $known = [400, 401, 403, 404, 405, 408, 419, 422, 429, 500, 502, 503, 504];

        return in_array($e->getCode(), $known, true)
            ? $e->getCode()
            : 500;
    }
}
