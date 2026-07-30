<?php

declare(strict_types=1);

namespace App\Core\Container;

use Closure;

/**
 * Fluent builder for contextual bindings.
 *
 * Usage:
 *   $container->when(Handler::class)
 *       ->needs(LoggerInterface::class)
 *       ->give(FileLogger::class);
 *
 * This allows different consumers to receive different implementations
 * of the same interface, without affecting the global binding.
 */
class ContextualBindingBuilder
{
    /**
     * The abstract type that the consumer needs injected.
     */
    protected string $needs;

    /**
     * @param Container $container The DI container instance.
     * @param string    $concrete  The consumer class requiring contextual injection.
     */
    public function __construct(
        protected Container $container,
        protected string $concrete
    ) {
    }

    /**
     * Declare the abstract type that the consumer class requires.
     *
     * @param string $abstract The interface or abstract class name.
     * @return $this
     */
    public function needs(string $abstract): self
    {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Provide the concrete implementation to inject for this context.
     *
     * @param class-string|Closure $implementation A class name or factory closure.
     */
    public function give(string|Closure $implementation): void
    {
        $this->container->addContextualBinding(
            $this->concrete,
            $this->needs,
            $implementation
        );
    }
}
