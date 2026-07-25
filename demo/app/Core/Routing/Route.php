<?php

namespace App\Core\Routing;

class Route
{
    public string $method;
    public string $uri;
    public $action;
    public array $middleware;
    public ?string $name = null;

    public function __construct(string $method, string $uri, $action, array $middleware)
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->middleware = $middleware;
    }

    public function name(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
