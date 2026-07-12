<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use App\Core\Container\Container;

abstract class TestCase extends PHPUnitTestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();

        if (session_status() === PHP_SESSION_NONE) {
            $_SESSION = [];
        }
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $_GET = [];
        $_POST = [];
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_URI'] = '/';
        $_SERVER['SCRIPT_NAME'] = '/index.php';
        $_SERVER['QUERY_STRING'] = '';
        $_SERVER['CONTENT_TYPE'] = '';
        $_SERVER['CONTENT_LENGTH'] = '';
        unset($_SERVER['HTTP_X_CSRF_TOKEN']);

        parent::tearDown();
    }

    protected function setRequestMethod(string $method): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
    }

    protected function setRequestUri(string $uri): void
    {
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['QUERY_STRING'] = parse_url($uri, PHP_URL_QUERY) ?? '';
    }

    protected function setRequestHeaders(array $headers): void
    {
        foreach ($headers as $key => $value) {
            $headerKey = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
            $_SERVER[$headerKey] = $value;
        }
    }

    protected function setJsonRequestBody(array $data): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/json';
        $_SERVER['HTTP_ACCEPT'] = 'application/json';
        $_POST = [];
    }

    protected function setFormRequestBody(array $data): void
    {
        $_SERVER['CONTENT_TYPE'] = 'application/x-www-form-urlencoded';
        $_POST = $data;
    }

    protected function withSessionData(array $data): void
    {
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    protected function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean();
    }
}
