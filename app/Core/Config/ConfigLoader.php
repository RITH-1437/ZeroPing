<?php

namespace App\Core\Config;

class ConfigLoader
{
    public function load(): array
    {
        $configs = [];

        $excluded = [
            'config.php',
            'constants.php',
            'routes.php',
        ];

        foreach (glob(BASE_PATH . '/config/*.php') as $file) {

            $name = basename($file);

            if (in_array($name, $excluded)) {
                continue;
            }

            $configs[basename($file, '.php')] = require $file;
        }

        return $configs;
    }
}