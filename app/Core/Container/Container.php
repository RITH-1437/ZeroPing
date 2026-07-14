<?php

namespace App\Core\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

class Container
{
    protected array $bindings = [];

    protected array $instances = [];

    /**
     * Contextual bindings keyed by [consumer][abstract] => concrete.
     *
     * @var array<string, array<string, class-string|Closure>>
     */
    protected array $contextual = [];

    /**
     * Resolving callbacks keyed by abstract (or '*' for any).
     *
     * @var array<string, array<int, Closure>>
     */
    protected array $resolvingCallbacks = [];

    /**
     * ReflectionClass cache keyed by class name.
     * Building a ReflectionClass is the dominant cost of make(); caching it
     * removes that cost for every subsequent resolution of the same class.
     *
     * @var array<string, \ReflectionClass>
     */
    protected static array $reflectionCache = [];

    /**
     * Bind a class.
     *
     * @param string $abstract
     * @param Closure|string $concrete
     */
    public function bind(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => false,
        ];
    }

    /**
     * Register a singleton.
     *
     * @param string $abstract
     * @param Closure|string $concrete
     */
    public function singleton(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared'   => true,
        ];
    }

    /**
     * Begin a contextual binding for the given consumer class.
     *
     * @param string $concrete
     * @return ContextualBindingBuilder
     */
    public function when(string $concrete): ContextualBindingBuilder
    {
        return new ContextualBindingBuilder($this, $concrete);
    }

    /**
     * Add a contextual binding.
     *
     * @param string $consumer
     * @param string $abstract
     * @param Closure|string $concrete
     */
    public function addContextualBinding(string $consumer, string $abstract, Closure|string $concrete): void
    {
        $this->contextual[$consumer][$abstract] = $concrete;
    }

    /**
     * Get a contextual binding.
     *
     * @param string $consumer
     * @param string $abstract
     * @return Closure|string|null
     */
    public function getContextualBinding(string $consumer, string $abstract): Closure|string|null
    {
        return $this->contextual[$consumer][$abstract] ?? null;
    }

    /**
     * Register a callback fired after an abstract is resolved.
     *
     * Used to boot deferred service providers on first use.
     *
     * @param string $abstract
     * @param Closure $callback
     */
    public function resolving(string $abstract, Closure $callback): void
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
    }

    protected function fireResolving(string $abstract, object $object): void
    {
        foreach ($this->resolvingCallbacks['*'] ?? [] as $callback) {
            $callback($object, $this);
        }

        foreach ($this->resolvingCallbacks[$abstract] ?? [] as $callback) {
            $callback($object, $this);
        }
    }

    /**
     * Whether an abstract is already bound or instantiated.
     *
     * @param string $abstract
     * @return bool
     */
    public function bound(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * Register an existing instance.
     *
     * @param string $abstract
     * @param object $instance
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a class (alias: make()).
     *
     * @param string $abstract
     * @return object
     */
    public function make(string $abstract): object
    {
        return $this->resolve($abstract);
    }

    /**
     * Resolve a class from the container.
     *
     * Supports concrete classes, interface bindings, contextual bindings
     * and convention-based auto-discovery of interface implementations
     * (e.g. Foo\BarInterface -> Foo\Bar).
     *
     * @param string $abstract
     * @return object
     */
    public function resolve(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            $object = $this->instances[$abstract];
        } elseif (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];

            $concrete = $binding['concrete'];

            $object = $concrete instanceof Closure
                ? $concrete($this)
                : $this->build($concrete);

            if ($binding['shared']) {
                $this->instances[$abstract] = $object;
            }
        } elseif (interface_exists($abstract)) {
            // Auto-discover an implementation for an unbound interface.
            $discovered = $this->discoverImplementation($abstract);

            if ($discovered !== null) {
                $this->bind($abstract, $discovered);

                return $this->resolve($abstract);
            }

            $object = $this->build($abstract);
        } else {
            $object = $this->build($abstract);
        }

        $this->fireResolving($abstract, $object);

        return $object;
    }

    /**
     * Convention-based implementation discovery for interfaces:
     * Foo\BarInterface resolves to Foo\Bar when that class exists.
     */
    protected function discoverImplementation(string $interface): ?string
    {
        if (str_ends_with($interface, 'Interface')) {
            $candidate = substr($interface, 0, -strlen('Interface'));

            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Build a class using reflection.
     */
    protected function build(string $class): object
    {
        if (!class_exists($class)) {
            throw new Exception("Class {$class} not found.");
        }

        if (!isset(self::$reflectionCache[$class])) {
            $reflection = new ReflectionClass($class);

            if (!$reflection->isInstantiable()) {
                throw new Exception("Class {$class} cannot be instantiated.");
            }

            self::$reflectionCache[$class] = $reflection;
        } else {
            $reflection = self::$reflectionCache[$class];
        }

        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            return new $class();
        }

        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {
            $dependencies[] = $this->resolveParameter($parameter);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    protected function resolveParameter(
        ReflectionParameter $parameter
    ): mixed {

        $type = $parameter->getType();

        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            throw new Exception(
                "Unable to resolve {$parameter->getName()}"
            );
        }

        $typeName = $type->getName();

        // Honour a contextual binding declared for the consuming class.
        $declaring = $parameter->getDeclaringClass();
        if ($declaring !== null) {
            $contextual = $this->getContextualBinding($declaring->getName(), $typeName);

            if ($contextual !== null) {
                return is_string($contextual)
                    ? $this->resolve($contextual)
                    : $contextual($this);
            }
        }

        return $this->resolve($typeName);
    }
}
