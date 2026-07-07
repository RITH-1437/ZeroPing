<?php

namespace App\Core\Filesystem;

class File
{
    protected string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function get(): string
    {
        return file_get_contents($this->path);
    }

    public function put(string $contents): bool
    {
        return file_put_contents($this->path, $contents) !== false;
    }

    public function delete(): bool
    {
        return unlink($this->path);
    }

    public function size(): int
    {
        return filesize($this->path);
    }

    public function lastModified(): int
    {
        return filemtime($this->path);
    }

    public function mimeType(): string
    {
        return mime_content_type($this->path);
    }

    public function extension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function name(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    public function basename(): string
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    public function dirname(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }
}
