<?php

declare(strict_types=1);

namespace App\Core\Packages;

/**
 * Starter kit definitions.
 *
 * A starter kit is a named bundle of ZeroPing packages that gets
 * enabled together, so users install a complete stack in one command
 * instead of adding packages one by one.
 */
class StarterKit
{
    /**
     * @return array<string, array{label:string, packages:string[], notes:string[]}>
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

    public static function exists(string $name): bool
    {
        return isset(self::kits()[$name]);
    }

    /**
     * @return string[]
     */
    public static function names(): array
    {
        return array_keys(self::kits());
    }
}
