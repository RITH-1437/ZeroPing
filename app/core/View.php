<?php

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        $viewPath = __DIR__ . '/../../views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View '{$view}' not found.");
        }

        require_once $viewPath;
    }
}