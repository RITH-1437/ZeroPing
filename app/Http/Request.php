<?php

namespace App\Http;

use App\Core\Filesystem\UploadedFile;

class Request
{
    public static function capture(): self
    {
        return new static();
    }

    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function isGet(): bool
    {
        return self::method() === 'GET';
    }

    public static function isPost(): bool
    {
        return self::method() === 'POST';
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    public static function all(): array
    {
        return array_merge($_GET, $_POST);
    }

    public static function has(string $key): bool
    {
        return isset($_POST[$key]) || isset($_GET[$key]);
    }

    public static function file(string $key): ?UploadedFile
    {
        if (isset($_FILES[$key])) {
            return new UploadedFile($_FILES[$key]);
        }

        return null;
    }

    public static function hasFile(string $key): bool
    {
        return isset($_FILES[$key]);
    }

    public static function files(): array
    {
        $files = [];

        foreach ($_FILES as $key => $file) {
            $files[$key] = new UploadedFile($file);
        }

        return $files;
    }

    public static function is(string $pattern): bool
    {
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        return fnmatch($pattern, $path);
    }

    public static function header(string $key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $_SERVER[$key] ?? $default;
    }

    public static function url(): string
    {
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
    }

    public static function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
