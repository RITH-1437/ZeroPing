<?php

if (!function_exists('render_component')) {
    function render_component(string $name, array $data = []): void
    {
        $component = __DIR__ . '/' . $name . '.php';

        if (!file_exists($component)) {
            return;
        }

        extract($data);
        require $component;
    }
}
