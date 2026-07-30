<?php

declare(strict_types=1);

namespace App\Core\Packages;

/**
 * Starter kit definitions.
 *
 * A starter kit is a named bundle of ZeroPing packages that gets
 * enabled together, so users install a complete stack in one command
 * instead of adding packages one by one.
 *
 * Kits are statically defined but can be extended or overridden
 * by providing a custom kits configuration in the future.
 */
final class StarterKit
{
    /**
     * Get all available starter kit definitions.
     *
     * Each kit contains:
     * - `label`: Human-readable description shown in CLI/UI.
     * - `packages`: List of package names to enable.
     * - `notes`: Summary of features included.
     *
     * @return array<string, array{label: string, packages: string[], notes: string[]}>
     *    Kits keyed by their slug identifier.
     */
    public static function kits(): array
    {
        return [
            'arena' => [
                'label' => 'Arena — community & forum starter',
                'packages' => [
                    'zeroping/auth',
                    'zeroping/admin',
                    'zeroping/blog',
                    'zeroping/queue',
                ],
                'notes' => [
                    'Authentication',
                    'Admin Panel',
                    'Blog / CMS',
                    'Background Queues',
                    'SQLite configured',
                ],
            ],
            'ecommerce' => [
                'label' => 'E-commerce starter',
                'packages' => [
                    'zeroping/auth',
                    'zeroping/payment',
                    'zeroping/admin',
                    'zeroping/queue',
                ],
                'notes' => [
                    'Authentication',
                    'Payments',
                    'Admin Panel',
                    'Background Queues',
                    'SQLite configured',
                ],
            ],
            'api' => [
                'label' => 'API starter',
                'packages' => [
                    'zeroping/auth',
                    'zeroping/queue',
                ],
                'notes' => [
                    'Authentication (token)',
                    'Background Queues',
                    'SQLite configured',
                ],
            ],
        ];
    }

    /**
     * Determine if a starter kit exists by name.
     *
     * @param string $name The kit slug to check.
     *
     * @return bool True if the kit is defined.
     */
    public static function exists(string $name): bool
    {
        return isset(self::kits()[$name]);
    }

    /**
     * Get all available kit slug names.
     *
     * @return array<int, string> List of kit identifiers.
     */
    public static function names(): array
    {
        return array_keys(self::kits());
    }

    /**
     * Get a specific starter kit definition.
     *
     * @param string $name The kit slug.
     *
     * @return array{label: string, packages: string[], notes: string[]}|null
     *    The kit definition, or null if not found.
     */
    public static function get(string $name): ?array
    {
        return self::kits()[$name] ?? null;
    }

    /**
     * Get the package list for a specific starter kit.
     *
     * @param string $name The kit slug.
     *
     * @return array<int, string> The list of package names, or empty array if kit not found.
     */
    public static function packages(string $name): array
    {
        $kit = self::get($name);

        return $kit['packages'] ?? [];
    }
}
