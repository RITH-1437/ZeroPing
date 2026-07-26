<?php

declare(strict_types=1);

namespace App\Core\View;

use App\Core\Http\Request;
use App\Core\Security\CSRF;
use App\Core\Session\Flash;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        ?string $layout = 'guest'
    ): string {
        return View::render($view, $data, $layout);
    }

    protected function validateCsrf(): bool
    {
        $token = Request::input('_token');
        if (!$token || !CSRF::check($token)) {
            Flash::error('Invalid security token. Please try again.');
            return false;
        }
        return true;
    }

    protected function redirect(string $url): never
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            $url = '/';
        }
        header('Location: ' . $url);
        exit;
    }

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
}
