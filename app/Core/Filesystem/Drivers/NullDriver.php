<?php

namespace App\Core\Filesystem\Drivers;

class NullDriver implements FilesystemDriver
{
    public function put(string $path, $contents): bool
    {
        return true;
    }

    public function get(string $path)
    {
        return null;
    }

    public function exists(string $path): bool
    {
        return false;
    }

    public function delete(string $path): bool
    {
        return true;
    }

    public function copy(string $from, string $to): bool
    {
        return true;
    }

    public function move(string $from, string $to): bool
    {
        return true;
    }

    public function size(string $path): int
    {
        return 0;
    }

    public function lastModified(string $path): int
    {
        return 0;
    }

    public function mimeType(string $path): string
    {
        return '';
    }

    public function url(string $path): string
    {
        return '';
    }

    public function download(string $path, ?string $name = null, array $headers = [])
    {
        //
    }

    public function files(?string $directory = null, bool $recursive = false): array
    {
        return [];
    }

    public function directories(?string $directory = null, bool $recursive = false): array
    {
        return [];
    }

    public function makeDirectory(string $path): bool
    {
        return true;
    }

    public function deleteDirectory(string $path): bool
    {
        return true;
    }
}
