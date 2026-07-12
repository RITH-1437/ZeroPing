<?php

namespace Zeroping\Support\Foundation;

/**
 * Tracks view namespaces registered by packages.
 *
 * Mirrors the proposed View::addNamespace() core adapter (ARCHITECTURE.md §6.1).
 * When the core adapter is present, ServiceProvider::loadViewsFrom() delegates
 * to it; otherwise this registry documents the intent and can be consumed by a
 * small core shim. Namespaced views resolve as "<namespace>::<view>".
 */
class ViewFinder
{
    /** @var array<string, string> */
    private static array $namespaces = [];

    public static function addNamespace(string $namespace, string $path): void
    {
        self::$namespaces[$namespace] = rtrim($path, '/');
    }

    public static function path(string $namespace): ?string
    {
        return self::$namespaces[$namespace] ?? null;
    }

    /**
     * @return array<string, string>
     */
    public static function all(): array
    {
        return self::$namespaces;
    }

    /**
     * Resolve a "namespace::view" identifier to a file path, or null.
     */
    public static function resolve(string $name): ?string
    {
        if (!str_contains($name, '::')) {
            return null;
        }

        [$ns, $view] = explode('::', $name, 2);
        $base = self::$namespaces[$ns] ?? null;

        if ($base === null) {
            return null;
        }

        $file = $base . '/' . str_replace('.', '/', $view) . '.php';

        return file_exists($file) ? $file : null;
    }
}
