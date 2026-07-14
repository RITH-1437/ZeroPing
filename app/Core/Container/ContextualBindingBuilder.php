<?php

namespace App\Core\Container;

use Closure;

/**
 * Fluent builder for contextual bindings.
 *
 *   $container->when(Handler::class)
 *       ->needs(Logger::class)
 *       ->give(FileLogger::class);
 */
class ContextualBindingBuilder
{
    protected string $needs;

    public function __construct(
        protected Container $container,
        protected string $concrete
    ) {
    }

    public function needs(string $abstract): self
    {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * @param class-string|Closure $implementation
     */
    public function give(string|Closure $implementation): void
    {
        $this->container->addContextualBinding($this->concrete, $this->needs, $implementation);
    }
}
