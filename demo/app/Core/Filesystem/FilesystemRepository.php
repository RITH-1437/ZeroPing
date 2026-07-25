<?php

namespace App\Core\Filesystem;

use App\Core\Filesystem\Drivers\FilesystemDriver;

class FilesystemRepository
{
    protected FilesystemDriver $driver;

    public function __construct(FilesystemDriver $driver)
    {
        $this->driver = $driver;
    }

    public function put(string $path, $contents): bool
    {
        return $this->driver->put($path, $contents);
    }

    public function get(string $path)
    {
        return $this->driver->get($path);
    }

    public function exists(string $path): bool
    {
        return $this->driver->exists($path);
    }

    public function delete(string $path): bool
    {
        return $this->driver->delete($path);
    }

    public function copy(string $from, string $to): bool
    {
        return $this->driver->copy($from, $to);
    }

    public function move(string $from, string $to): bool
    {
        return $this->driver->move($from, $to);
    }

    public function size(string $path): int
    {
        return $this->driver->size($path);
    }

    public function lastModified(string $path): int
    {
        return $this->driver->lastModified($path);
    }

    public function mimeType(string $path): string
    {
        return $this->driver->mimeType($path);
    }

    public function url(string $path): string
    {
        return $this->driver->url($path);
    }

    public function download(string $path, ?string $name = null, array $headers = [])
    {
        return $this->driver->download($path, $name, $headers);
    }

    public function files(string $directory = '', bool $recursive = false): array
    {
        return $this->driver->files($directory, $recursive);
    }

    public function directories(string $directory = '', bool $recursive = false): array
    {
        return $this->driver->directories($directory, $recursive);
    }

    public function makeDirectory(string $path): bool
    {
        return $this->driver->makeDirectory($path);
    }

    public function deleteDirectory(string $path): bool
    {
        return $this->driver->deleteDirectory($path);
    }
}
