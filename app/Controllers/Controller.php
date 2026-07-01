<?php

namespace App\Core;

class Controller
{
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'guest'
    ): void {
        View::render($view, $data, $layout);
    }
}