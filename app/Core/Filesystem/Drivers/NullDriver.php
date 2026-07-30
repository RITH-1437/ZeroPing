<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Drivers;

/**
 * Null filesystem driver (black-hole / no-op).
 *
 * All write operations succeed silently and all read operations return
 * empty/default values. Useful for testing, or as a fallback when no
 * real storage is configured.
 */
class NullDriver implements FilesystemDriver
{
    /**
     * {@inheritDoc}
     */
    public function put(string $path, string $contents): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $path): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $path): bool
    {
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $path): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function copy(string $from, string $to): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function move(string $from, string $to): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function size(string $path): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function lastModified(string $path): int
    {
        return 0;
    }

    /**
     * {@inheritDoc}
     */
    public function mimeType(string $path): string
    {
        return 'application/octet-stream';
    }

    /**
     * {@inheritDoc}
     */
    public function url(string $path): string
    {
        return '';
    }

    /**
     * {@inheritDoc}
     */
    public function download(string $path, ?string $name = null, array $headers = []): void
    {
        // No-op: null driver does not emit any response.
    }

    /**
     * {@inheritDoc}
     */
    public function files(string $directory = '', bool $recursive = false): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function directories(string $directory = '', bool $recursive = false): array
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function makeDirectory(string $path): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDirectory(string $path): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function append(string $path, string $contents): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(string $path, string $contents): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getVisibility(string $path): string
    {
        return 'public';
    }

    /**
     * {@inheritDoc}
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        return true;
    }
}
