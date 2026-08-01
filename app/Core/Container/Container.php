<?php

declare(strict_types=1);

namespace App\Core\Container;

use Closure;
use Exception;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * Lightweight dependency-injection container with auto-wiring.
 *
 * Supports singleton/transient bindings, contextual bindings, resolving
 * callbacks, convention-based interface discovery, and PSR-11-style `has()`.
 */
class Container
{
    /**
     * Registered bindings keyed by abstract name.
     *
     * @var array<string, array{concrete: Closure|class-string, shared: bool}>
     */
    protected array $bindings = [];

    /**
     * Resolved singleton instances keyed by abstract name.
     *
     * @var array<string, object>
     */
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
     *
     * Building a ReflectionClass is the dominant cost of make(); caching it
     * removes that cost for every subsequent resolution of the same class.
     *
     * @var array<string, ReflectionClass<object>>
     */
    protected static array $reflectionCache = [];

    /**
     * Constructor parameter metadata cache.
     *
     * Stores extracted type info for constructor parameters so reflection
     * is only performed once per class, even across different container instances.
     *
     * @var array<string, array<int, array{
     *     name: string, type: string|null, builtin: bool,
     *     hasDefault: bool, default: mixed, declaringClass: string|null
     * }>|null>
     */
    protected static array $parameterCache = [];

    /**
     * Track whether any resolving callbacks are registered.
     * Avoids method call overhead when no callbacks exist.
     */
    protected bool $hasResolvingCallbacks = false;

    // -------------------------------------------------------------------------
    //  Binding
    // -------------------------------------------------------------------------

    /**
     * Bind an abstract type to a concrete implementation (transient).
     *
     * @param string $abstract The abstract type or interface name.
     * @param Closure|class-string $concrete Factory closure or class name.
     */
    public function bind(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared'   => false,
        ];
    }

    /**
     * Register a shared (singleton) binding.
     *
     * The concrete is resolved once and the same instance is returned on
     * subsequent calls to make()/resolve().
     *
     * @param string $abstract The abstract type or interface name.
     * @param Closure|class-string $concrete Factory closure or class name.
     */
    public function singleton(string $abstract, Closure|string $concrete): void
    {
        $this->bindings[$abstract] = [
            'concrete' => $concrete,
            'shared'   => true,
        ];
    }

    /**
     * Register an existing instance in the container.
     *
     * @param string $abstract The abstract type or interface name.
     * @param object $instance The pre-built instance.
     */
    public function instance(string $abstract, object $instance): void
    {
        $this->instances[$abstract] = $instance;
    }

    // -------------------------------------------------------------------------
    //  Contextual Bindings
    // -------------------------------------------------------------------------

    /**
     * Begin a contextual binding for the given consumer class.
     *
     * Usage:
     *   $container->when(Handler::class)
     *       ->needs(LoggerInterface::class)
     *       ->give(FileLogger::class);
     *
     * @param string $concrete The consumer class that requires contextual injection.
     */
    public function when(string $concrete): ContextualBindingBuilder
    {
        return new ContextualBindingBuilder($this, $concrete);
    }

    /**
     * Add a contextual binding (called by ContextualBindingBuilder).
     *
     * @param string $consumer The class that consumes the dependency.
     * @param string $abstract The abstract type being injected.
     * @param Closure|class-string $concrete The implementation to provide.
     */
    public function addContextualBinding(string $consumer, string $abstract, Closure|string $concrete): void
    {
        $this->contextual[$consumer][$abstract] = $concrete;
    }

    /**
     * Get a contextual binding for a consumer/abstract pair.
     *
     * @param string $consumer The consuming class name.
     * @param string $abstract The abstract type being resolved.
     * @return Closure|class-string|null The contextual concrete, or null.
     */
    public function getContextualBinding(string $consumer, string $abstract): Closure|string|null
    {
        return $this->contextual[$consumer][$abstract] ?? null;
    }

    // -------------------------------------------------------------------------
    //  Resolving Callbacks
    // -------------------------------------------------------------------------

    /**
     * Register a callback fired after an abstract is resolved.
     *
     * Use '*' as the abstract to fire for every resolution.
     * Useful for booting deferred service providers on first use.
     *
     * @param string $abstract The abstract name or '*' for global.
     * @param Closure $callback Receives (object $resolved, Container $container).
     */
    public function resolving(string $abstract, Closure $callback): void
    {
        $this->resolvingCallbacks[$abstract][] = $callback;
        $this->hasResolvingCallbacks = true;
    }

    /**
     * Fire resolving callbacks for a freshly resolved object.
     *
     * @param string $abstract The abstract that was resolved.
     * @param object $object The resolved instance.
     */
    protected function fireResolving(string $abstract, object $object): void
    {
        // Fast exit when no callbacks are registered at all.
        if (!$this->hasResolvingCallbacks) {
            return;
        }

        if (isset($this->resolvingCallbacks['*'])) {
            foreach ($this->resolvingCallbacks['*'] as $callback) {
                $callback($object, $this);
            }
        }

        if (isset($this->resolvingCallbacks[$abstract])) {
            foreach ($this->resolvingCallbacks[$abstract] as $callback) {
                $callback($object, $this);
            }
        }
    }

    // -------------------------------------------------------------------------
    //  Introspection
    // -------------------------------------------------------------------------

    /**
     * Whether an abstract is already bound or instantiated.
     *
     * @param string $abstract The abstract type to check.
     */
    public function bound(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    /**
     * PSR-11-compatible alias for bound().
     *
     * @param string $abstract The abstract type to check.
     */
    public function has(string $abstract): bool
    {
        return isset($this->instances[$abstract]) || isset($this->bindings[$abstract]);
    }

    // -------------------------------------------------------------------------
    //  Resolution
    // -------------------------------------------------------------------------

    /**
     * Resolve a class from the container.
     *
     * @param class-string|string $abstract The abstract type or class name.
* @return mixed The resolved instance.
      *
      * @throws Exception When the class cannot be resolved or built.
      */
     public function make(string $abstract): mixed
    {
        // Hot path: cached singleton — inline check avoids method call overhead.
        if (isset($this->instances[$abstract])) {
            $object = $this->instances[$abstract];
            if ($this->hasResolvingCallbacks) {
                $this->fireResolving($abstract, $object);
            }
            return $object;
        }

        return $this->resolve($abstract);
    }

    /**
     * Resolve a class from the container.
     *
     * Supports concrete classes, interface bindings, contextual bindings
     * and convention-based auto-discovery of interface implementations
     * (e.g. Foo\BarInterface → Foo\Bar).
     *
     * @param class-string|string $abstract The abstract type or class name.
* @return mixed The resolved instance.
      *
      * @throws Exception When the class cannot be resolved or built.
      */
     public function resolve(string $abstract): mixed
    {
        // 1. Return a cached singleton instance (fastest path).
        if (isset($this->instances[$abstract])) {
            $object = $this->instances[$abstract];
            if ($this->hasResolvingCallbacks) {
                $this->fireResolving($abstract, $object);
            }
            return $object;
        }

        // 2. Resolve via an explicit binding.
        if (isset($this->bindings[$abstract])) {
            $binding  = $this->bindings[$abstract];
            $concrete = $binding['concrete'];

            $object = $concrete instanceof Closure
                ? $concrete($this)
                : $this->build($concrete);

            if ($binding['shared']) {
                $this->instances[$abstract] = $object;
            }

            if ($this->hasResolvingCallbacks) {
                $this->fireResolving($abstract, $object);
            }
            return $object;
        }

        // 3. Auto-discover an implementation for an unbound interface.
        if (interface_exists($abstract)) {
            $object = $this->resolveInterface($abstract);
            if ($this->hasResolvingCallbacks) {
                $this->fireResolving($abstract, $object);
            }
            return $object;
        }

        // 4. Build the concrete class directly.
        if (!class_exists($abstract)) {
            throw new Exception(
                "Container: Cannot resolve [{$abstract}] — class does not exist. "
                . 'Check the namespace and ensure the class is autoloadable.'
            );
        }

        /** @var class-string $abstract */
        $object = $this->build($abstract);
        if ($this->hasResolvingCallbacks) {
            $this->fireResolving($abstract, $object);
        }
        return $object;
    }

    /**
     * Resolve an unbound interface via convention-based discovery.
     *
     * @param string $abstract The interface name.
* @return mixed The resolved instance.
      *
      * @throws Exception When no implementation can be discovered.
      */
     protected function resolveInterface(string $abstract): mixed
    {
        $discovered = $this->discoverImplementation($abstract);

        if ($discovered !== null) {
            $this->bind($abstract, $discovered);

            $binding  = $this->bindings[$abstract];
            $concrete = $binding['concrete'];

            return $concrete instanceof Closure
                ? $concrete($this)
                : $this->build($concrete);
        }

        // Fall through to build() – will throw if the interface is not
        // instantiable (which interfaces never are), giving a clear error.
        return $this->build($abstract);
    }

    /**
     * Convention-based implementation discovery for interfaces.
     *
     * If the interface name ends with "Interface", the container checks
     * whether a class with the suffix removed exists (e.g.
     * App\Contracts\LoggerInterface → App\Contracts\Logger).
     *
     * @param string $interface The fully-qualified interface name.
     * @return class-string|null The discovered class or null.
     */
    protected function discoverImplementation(string $interface): ?string
    {
        if (str_ends_with($interface, 'Interface')) {
            $candidate = substr($interface, 0, -9); // strlen('Interface') === 9

            if (class_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    // -------------------------------------------------------------------------
    //  Building
    // -------------------------------------------------------------------------

/**
      * Build a class using reflection and auto-wire its dependencies.
      *
      * @param  string  $class The fully-qualified class name.
      * @return mixed The constructed instance.
      *
      * @throws Exception When the class does not exist or cannot be instantiated.
      */
      protected function build(string $class): mixed
    {
        if (!class_exists($class)) {
            throw new Exception(
                "Container: Cannot resolve [{$class}] — class does not exist. "
                . 'Check the namespace and ensure the class is autoloadable.'
            );
        }

        $reflection = $this->getReflection($class);

        if (!$reflection->isInstantiable()) {
            throw new Exception(
                "Container: Cannot instantiate [{$class}] — it is abstract or an interface. "
                . 'Register a concrete binding via bind() or singleton().'
            );
        }

        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return new $class();
        }

        // Use cached parameter metadata to avoid re-reflecting on each build.
        $paramsMeta = $this->getParameterMeta($class, $constructor);

        if ($paramsMeta === null) {
            // No parameters — just instantiate.
            return new $class();
        }

        $dependencies = [];
        foreach ($paramsMeta as $meta) {
            $dependencies[] = $this->resolveParameterFromMeta($meta);
        }

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Get (or cache) the ReflectionClass for a given class name.
     *
     * @param class-string $class The class to reflect.
     * @return ReflectionClass<object> The cached reflection.
     */
    protected function getReflection(string $class): ReflectionClass
    {
        return self::$reflectionCache[$class] ??= new ReflectionClass($class);
    }

    /**
     * Get cached constructor parameter metadata for a class.
     *
     * Returns null if the constructor has no parameters.
     *
     * @param string $class
     * @param \ReflectionMethod $constructor
     * @return array<int, array{
     *     name: string, type: string|null, builtin: bool,
     *     hasDefault: bool, default: mixed, declaringClass: string|null
     * }>|null
     */
    protected function getParameterMeta(string $class, \ReflectionMethod $constructor): ?array
    {
        if (array_key_exists($class, self::$parameterCache)) {
            return self::$parameterCache[$class];
        }

        $params = $constructor->getParameters();
        if ($params === []) {
            self::$parameterCache[$class] = null;
            return null;
        }

        $meta = [];
        foreach ($params as $param) {
            $type = $param->getType();
            $isNamedType = $type instanceof ReflectionNamedType;

            $meta[] = [
                'name'           => $param->getName(),
                'type'           => $isNamedType ? $type->getName() : null,
                'builtin'        => $isNamedType ? $type->isBuiltin() : true,
                'hasDefault'     => $param->isDefaultValueAvailable(),
                'default'        => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null,
                'declaringClass' => $param->getDeclaringClass()?->getName(),
            ];
        }

        self::$parameterCache[$class] = $meta;
        return $meta;
    }

    /**
     * Resolve a single constructor parameter from cached metadata.
     *
     * @param array{
     *     name: string, type: string|null, builtin: bool,
     *     hasDefault: bool, default: mixed, declaringClass: string|null
     * } $meta
     * @return mixed The resolved value.
     *
     * @throws Exception When the parameter cannot be resolved.
     */
    protected function resolveParameterFromMeta(array $meta): mixed
    {
        // Non-typed or built-in (string, int, etc.) — use default or throw.
        if ($meta['type'] === null || $meta['builtin']) {
            if ($meta['hasDefault']) {
                return $meta['default'];
            }

            throw new Exception(
                "Container: Cannot resolve parameter [\${$meta['name']}] "
                . "in [{$meta['declaringClass']}] — no type-hint or default value provided."
            );
        }

        $typeName = $meta['type'];

        // Honour a contextual binding declared for the consuming class.
        if ($meta['declaringClass'] !== null && isset($this->contextual[$meta['declaringClass']][$typeName])) {
            $contextual = $this->contextual[$meta['declaringClass']][$typeName];
            return is_string($contextual)
                ? $this->resolve($contextual)
                : $contextual($this);
        }

        return $this->resolve($typeName);
    }

    /**
     * Resolve a single constructor parameter (legacy — used if parameter cache is bypassed).
     *
     * Handles typed class/interface parameters (with contextual binding
     * support) and falls back to default values for unresolvable primitives.
     *
     * @param ReflectionParameter $parameter The parameter to resolve.
     * @return mixed The resolved value.
     *
     * @throws Exception When the parameter cannot be resolved.
     */
    protected function resolveParameter(ReflectionParameter $parameter): mixed
    {
        $type = $parameter->getType();

        // Non-typed or built-in (string, int, etc.) — use default or throw.
        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            $declaringClass = $parameter->getDeclaringClass()?->getName() ?? 'unknown';

            throw new Exception(
                "Container: Cannot resolve parameter [\${$parameter->getName()}] "
                . "in [{$declaringClass}] — no type-hint or default value provided."
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

    // -------------------------------------------------------------------------
    //  State Management (useful for testing)
    // -------------------------------------------------------------------------

    /**
     * Remove a cached singleton instance.
     *
     * The binding is preserved, so the next make() call will re-resolve it.
     * Useful in tests to reset shared state between assertions.
     *
     * @param string $abstract The abstract to forget.
     */
    public function forgetInstance(string $abstract): void
    {
        unset($this->instances[$abstract]);
    }

    /**
     * Flush all bindings, instances, contextual bindings, and callbacks.
     *
     * Resets the container to a pristine state. The static reflection cache
     * is intentionally kept — call clearReflectionCache() to wipe it.
     */
    public function flush(): void
    {
        $this->bindings           = [];
        $this->instances          = [];
        $this->contextual         = [];
        $this->resolvingCallbacks = [];
        $this->hasResolvingCallbacks = false;
    }

    /**
     * Clear the static reflection cache.
     *
     * Normally unnecessary, but can be useful in long-running test suites
     * to reclaim memory or after dynamic class generation.
     */
    public static function clearReflectionCache(): void
    {
        self::$reflectionCache = [];
        self::$parameterCache = [];
    }
}
