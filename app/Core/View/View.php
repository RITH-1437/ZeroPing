<?php

namespace App\Core\View;

class View
{
    public static function render(
        string $view,
        array $data = [],
        string $layout = 'guest'
    ): void {

        extract($data);

        $viewFile = __DIR__ . "/../../views/{$view}.php";

        if (!file_exists($viewFile)) {
            die("View {$view} not found.");
        }

        ob_start();

        require $viewFile;

        $content = ob_get_clean();

        $layoutFile = __DIR__ . "/../../views/layouts/{$layout}.php";

        if (!file_exists($layoutFile)) {
            die("Layout {$layout} not found.");
        }

        require $layoutFile;
    }
}