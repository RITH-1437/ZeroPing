<?php

declare(strict_types=1);

namespace App\Core\Routing;

/**
 * Represents a single registered route.
 *
 * Instances are created by the Router and stored in the route table.
 * Route objects are immutable in their core identity (method, uri) but
 * allow fluent configuration of middleware and name after construction.
 */
class Route
{
    /**
     * HTTP method (GET, POST, PUT, PATCH, DELETE, OPTIONS, ANY).
     */
    public readonly string $method;

    /**
     * The URI pattern (e.g. "/users/{id}").
     */
    public readonly string $uri;

    /**
     * The action — either a [Controller::class, 'method'] array or a Closure.
     *
     * @var array{0: class-string, 1: string}|\Closure
     */
    public readonly array|\Closure $action;

    /**
     * Middleware assigned to this route (short names or FQCNs).
     *
     * @var array<int, string>
     */
    public array $middleware;

    /**
     * The route name for URL generation, or null if unnamed.
     */
    public ?string $name = null;

    /**
     * Create a new Route instance.
     *
     * @param string                                   $method      HTTP method.
     * @param string                                   $uri         URI pattern.
     * @param array{0: class-string, 1: string}|\Closure $action      Controller action or closure.
     * @param array<int, string>                        $middleware  Middleware list.
     */
    public function __construct(
        string $method,
        string $uri,
        array|\Closure $action,
        array $middleware = []
    ) {
        $this->method     = $method;
        $this->uri        = $uri;
        $this->action     = $action;
        $this->middleware = $middleware;
    }

    /**
     * Assign a name to this route (fluent).
     *
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Append middleware to this route (fluent).
     *
     * @param string|array<int, string> $middleware  One or more middleware names.
     * @return $this
     */
    public function withMiddleware(string|array $middleware): self
    {
        $middleware = is_array($middleware) ? $middleware : [$middleware];

        $this->middleware = array_merge($this->middleware, $middleware);

        return $this;
    }

    /**
     * Assign a name to this route (alias for name(), fluent).
     *
     * @param string $name
     * @return $this
     */
    public function withName(string $name): self
    {
        return $this->name($name);
    }

    /**
     * Get the route name.
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Get the HTTP method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the URI pattern.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the action (controller pair or closure).
     *
     * @return array{0: class-string, 1: string}|\Closure
     */
    public function getAction(): array|\Closure
    {
        return $this->action;
    }

    /**
     * Get the assigned middleware.
     *
     * @return array<int, string>
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }
}
