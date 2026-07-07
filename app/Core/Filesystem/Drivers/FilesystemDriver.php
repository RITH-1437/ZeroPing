<?php

namespace App\Core\Filesystem\Drivers;

interface FilesystemDriver
{
    public function put(string $path, $contents): bool;

    public function get(string $path);

    public function exists(string $path): bool;

    public function delete(string $path): bool;

    public function copy(string $from, string $to): bool;

    public function move(string $from, string $to): bool;

    public function size(string $path): int;

    public function lastModified(string $path): int;

    public function mimeType(string $path): string;

    public function url(string $path): string;

    public function download(string $path, string $name = null, array $headers = []);

    public function files(string $directory = null, bool $recursive = false): array;

    public function directories(string $directory = null, bool $recursive = false): array;

    public function makeDirectory(string $path): bool;

    public function deleteDirectory(string $path): bool;
}
