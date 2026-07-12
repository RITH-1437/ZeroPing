<?php

namespace Zeroping\Support\Foundation;

/**
 * Aggregates migration directories from all registered packages.
 *
 * The framework's migrate commands should call paths() and scan each directory
 * (the same way they currently scan database/migrations). This makes package
 * migrations first-class without changing the migration runner's logic.
 */
class MigrationLoader
{
    /** @var string[] */
    private static array $paths = [];

    public static function addPath(string $path): void
    {
        $path = rtrim($path, '/');

        if (!in_array($path, self::$paths, true)) {
            self::$paths[] = $path;
        }
    }

    /**
     * @return string[]
     */
    public static function paths(): array
    {
        return self::$paths;
    }

    public static function clear(): void
    {
        self::$paths = [];
    }
}
