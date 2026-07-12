<?php

namespace Zeroping\Support\Console;

/**
 * Registry of package console commands.
 *
 * Package service providers push their command classes here via
 * ServiceProvider::commands(). The framework Console consults this registry
 * (by command name) before falling back to its built-in switch.
 */
class CommandRegistry
{
    /** @var array<string, class-string<Command>> */
    private static array $commands = [];

    /**
     * @param class-string<Command> $class
     */
    public static function register(string $class): void
    {
        if (!is_subclass_of($class, Command::class, true)) {
            throw new \InvalidArgumentException(
                "Command {$class} must extend Zeroping\\Support\\Console\\Command"
            );
        }

        /** @var Command $instance */
        $instance = new $class();
        self::$commands[$instance->name()] = $class;
    }

    /**
     * @return class-string<Command>|null
     */
    public static function find(string $name): ?string
    {
        return self::$commands[$name] ?? null;
    }

    /**
     * @return array<string, class-string<Command>>
     */
    public static function all(): array
    {
        return self::$commands;
    }

    public static function run(string $name): bool
    {
        $class = self::find($name);

        if ($class === null) {
            return false;
        }

        (new $class())->handle();
        return true;
    }
}
