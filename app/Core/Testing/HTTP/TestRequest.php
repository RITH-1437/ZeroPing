<?php

namespace App\Core\Testing\HTTP;

class TestRequest
{
    protected string $method;
    protected string $uri;
    protected array $parameters;
    protected array $cookies;
    protected array $files;
    protected array $server;
    protected string $content;

    public function __construct(string $method, string $uri, array $parameters = [], array $cookies = [], array $files = [], array $server = [], string $content = null)
    {
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
        // This is a simplified implementation. A real implementation would
        // create a new request and dispatch it through the router.
        ob_start();
        // The router would be called here.
        $content = ob_get_clean();

        return new TestResponse($content, 200, []);
    }
}
