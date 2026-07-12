<?php

namespace App\Core\View;

use App\Core\Http\Request;
use App\Core\Security\CSRF;
use App\Core\Session\Flash;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'guest'
    ): void {
        View::render($view, $data, $layout);
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
        header('Location: ' . $url);
        exit;
    }

    protected function redirectBack(string $fallback = '/'): never
    {
        $url = $_SERVER['HTTP_REFERER'] ?? $fallback;
        header('Location: ' . $url);
        exit;
    }
}