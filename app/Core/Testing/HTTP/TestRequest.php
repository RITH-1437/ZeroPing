<?php

namespace App\Core\Testing\HTTP;

use App\Core\Application\App;
use App\Core\Http\Kernel;
use App\Core\Http\Response;

class TestRequest
{
    protected string $method;
    protected string $uri;
    protected array $parameters;
    protected array $cookies;
    protected array $files;
    protected array $server;
    protected string $content;

    public function __construct(
        string $method,
        string $uri,
        array $parameters = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        string $content = ''
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->parameters = $parameters;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
        $this->content = $content;
    }

    public function send(): TestResponse
    {
        $originalServer = $_SERVER;
        $originalGet = $_GET;
        $originalPost = $_POST;

        try {
            $_SERVER['REQUEST_METHOD'] = $this->method;
            $_SERVER['REQUEST_URI'] = $this->uri;

            foreach ($this->server as $key => $value) {
                $_SERVER[$key] = $value;
            }

            if ($this->method === 'GET') {
                $_GET = $this->parameters;
                $_POST = [];
            } else {
                $_POST = $this->parameters;
                $_GET = [];
            }

            Response::resetLastSent();

            $kernelClass = class_exists('App\\Http\\Kernel')
                ? 'App\\Http\\Kernel'
                : Kernel::class;

            $kernel = new $kernelClass(App::container());
            ob_start();
            $kernel->handle();
            $content = ob_get_clean();

            $sent = Response::lastSent();

            return new TestResponse(
                $content,
                $sent['status'] ?? 200,
                $sent['headers'] ?? []
            );
        } finally {
            $_SERVER = $originalServer;
            $_GET = $originalGet;
            $_POST = $originalPost;
        }
    }
}
