<?php

declare(strict_types=1);

namespace App\Core\Console;

/**
 * Central registry that maps command signatures to their implementing classes.
 *
 * The registry replaces the monolithic switch statement that previously lived in
 * Console::run(). Commands self-register via their `$signature` property, and
 * the registry handles lookup, auto-discovery from the Commands/ and Generators/
 * directories, and integration with the package-level command registry.
 *
 * @package App\Core\Console
 */
class CommandRegistry
{
    /**
     * Map of command signature => fully-qualified class name.
     *
     * @var array<string, class-string<Command>>
     */
    private array $commands = [];

    /**
     * Whether auto-discovery has already been performed.
     */
    private bool $discovered = false;

    /**
     * Register a command class by its signature.
     *
     * @param class-string<Command> $class
     */
    public function register(string $signature, string $class): void
    {
        $this->commands[$signature] = $class;
    }

    /**
     * Register multiple commands at once.
     *
     * @param array<string, class-string<Command>> $commands
     */
    public function registerMany(array $commands): void
    {
        foreach ($commands as $signature => $class) {
            $this->commands[$signature] = $class;
        }
    }

    /**
     * Resolve a command class by signature, returning null if not found.
     *
     * @return class-string<Command>|null
     */
    public function resolve(string $signature): ?string
    {
        $this->ensureDiscovered();

        return $this->commands[$signature] ?? null;
    }

    /**
     * Check whether a command is registered.
     */
    public function has(string $signature): bool
    {
        $this->ensureDiscovered();

        return isset($this->commands[$signature]);
    }

    /**
     * Return all registered commands keyed by signature.
     *
     * @return array<string, class-string<Command>>
     */
    public function all(): array
    {
        $this->ensureDiscovered();

        return $this->commands;
    }

    /**
     * Run auto-discovery if not yet done.
     */
    private function ensureDiscovered(): void
    {
        if (!$this->discovered) {
            $this->discover();
            $this->discovered = true;
        }
    }

    /**
     * Auto-discover command classes from the framework's Commands/ and
     * Generators/ directories. Each class that extends Command and defines
     * a non-empty $signature property is registered automatically.
     */
    public function discover(): void
    {
        $directories = [
            __DIR__ . '/Commands'   => 'App\\Core\\Console\\Commands\\',
            __DIR__ . '/Generators' => 'App\\Core\\Console\\Generators\\',
        ];

        foreach ($directories as $directory => $namespace) {
            if (!is_dir($directory)) {
                continue;
            }

            $files = glob($directory . '/*.php');

            if ($files === false) {
                continue;
            }

            foreach ($files as $file) {
                $className = $namespace . pathinfo($file, PATHINFO_FILENAME);

                // Skip abstract classes and non-Command classes
                if (!class_exists($className)) {
                    continue;
                }

                $reflection = new \ReflectionClass($className);

                if ($reflection->isAbstract()) {
                    continue;
                }

                if (!$reflection->isSubclassOf(Command::class)) {
                    continue;
                }

                // Extract signature from the class without instantiating
                $signature = $this->extractSignature($reflection);

                if ($signature !== null && $signature !== '') {
                    $this->commands[$signature] = $className;
                }
            }
        }
    }

    /**
     * Extract the command signature from a reflected class.
     *
     * Reads the protected $signature property directly via reflection to avoid
     * instantiation side effects (e.g. option parsing, output setup).
     *
     * @param \ReflectionClass<Command> $reflection
     */
    private function extractSignature(\ReflectionClass $reflection): ?string
    {
        if (!$reflection->hasProperty('signature')) {
            return null;
        }

        $property = $reflection->getProperty('signature');
        $property->setAccessible(true);

        // Get the default value without instantiating
        $defaults = $reflection->getDefaultProperties();

        return $defaults['signature'] ?? null;
    }

    /**
     * Attempt to resolve a command from the package-level registry (external packages).
     *
     * @return class-string|null
     */
    public function resolveFromPackages(string $signature): ?string
    {
        if (!class_exists(\Zeroping\Support\Console\CommandRegistry::class)) {
            return null;
        }

        return \Zeroping\Support\Console\CommandRegistry::find($signature);
    }

    /**
     * Get all commands registered by packages.
     *
     * @return array<string, class-string>
     */
    public function allFromPackages(): array
    {
        if (!class_exists(\Zeroping\Support\Console\CommandRegistry::class)) {
            return [];
        }

        return \Zeroping\Support\Console\CommandRegistry::all();
    }
}
