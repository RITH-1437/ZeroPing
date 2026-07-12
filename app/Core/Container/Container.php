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
     * ReflectionClass cache keyed by class name.
     * Building a ReflectionClass is the dominant cost of make(); caching it
     * removes that cost for every subsequent resolution of the same class.
     *
     * @var array<string, \ReflectionClass>
     */
    protected static array $reflectionCache = [];

    /**
     * Bind a class.
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
     */
    public function singleton(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared' => true,
        ];
    }

    /**
     * Register an existing instance.
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    /**
     * Resolve a class.
     */
    public function make(string $abstract): object
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            $binding = $this->bindings[$abstract];

            $concrete = $binding['concrete'];

            $object = $concrete instanceof Closure
                ? $concrete($this)
                : $this->build($concrete);

            if ($binding['shared']) {
                $this->instances[$abstract] = $object;
            }

            return $object;
        }

        return $this->build($abstract);
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

        return $this->make($type->getName());
    }
}
