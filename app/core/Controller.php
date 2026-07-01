<?php

require_once __DIR__ . '/View.php';

class Controller
{
    protected function view(string $view, array $data = []): void
    {
        View::render($view, $data);
    }
}