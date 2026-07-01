<?php

namespace App\Support;

use Exception;

class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) {
            throw new Exception(".env file not found.");
        }

        $lines = file(
            $path,
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        foreach ($lines as $line) {

            if (str_starts_with(trim($line), '#')) {
                continue;
            }

            [$key, $value] = array_pad(
                explode('=', $line, 2),
                2,
                ''
            );

            $_ENV[trim($key)] = trim($value);
        }
    }
}