<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Drivers;

use App\Core\Filesystem\Exceptions\FilesystemException;
use App\Core\Support\Log;

class LocalDriver implements FilesystemDriver
{
    protected string $root;

    public function __construct(array $config)
    {
        $this->root = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $config['root']), DIRECTORY_SEPARATOR);
    }

    public function put(string $path, $contents): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectoryExists(dirname($location));

        return file_put_contents($location, $contents) !== false;
    }

    public function get(string $path)
    {
        $location = $this->applyPathPrefix($path);

        if (!$this->exists($path)) {
            throw new FilesystemException("File not found at path: {$path}");
        }

        return file_get_contents($location);
    }

    public function exists(string $path): bool
    {
        return file_exists($this->applyPathPrefix($path));
    }

    public function delete(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        if (file_exists($location)) {
            Log::info("File deleted: {$path}");
            return unlink($location);
        }

        return false;
    }

    public function copy(string $from, string $to): bool
    {
        $fromLocation = $this->applyPathPrefix($from);
        $toLocation = $this->applyPathPrefix($to);

        $this->ensureDirectoryExists(dirname($toLocation));

        return copy($fromLocation, $toLocation);
    }

    public function move(string $from, string $to): bool
    {
        return $this->copy($from, $to) && $this->delete($from);
    }

    public function size(string $path): int
    {
        return filesize($this->applyPathPrefix($path));
    }

    public function lastModified(string $path): int
    {
        return filemtime($this->applyPathPrefix($path));
    }

    public function mimeType(string $path): string
    {
        return mime_content_type($this->applyPathPrefix($path));
    }

    public function url(string $path): string
    {
        // This is a simplified implementation. A real implementation would
        // need to be aware of the public path and the base URL.
        return '/storage/' . $path;
    }

    public function download(string $path, ?string $name = null, array $headers = [])
    {
        $location = $this->applyPathPrefix($path);
        $name = $name ?: basename($location);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($location));

        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }

        readfile($location);
    }

    public function files(string $directory = '', bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        $items = new \FilesystemIterator($path);

        foreach ($items as $item) {
            if ($item->isFile()) {
                $files[] = $item->getFilename();
            } elseif ($recursive && $item->isDir()) {
                foreach ($this->files($item->getFilename(), true) as $sub) {
                    $files[] = $item->getFilename() . '/' . $sub;
                }
            }
        }

        return $files;
    }

    public function directories(string $directory = '', bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        $directories = [];
        $items = new \FilesystemIterator($path);

        foreach ($items as $item) {
            if ($item->isDir()) {
                $directories[] = $item->getFilename();
                if ($recursive) {
                    foreach ($this->directories($item->getFilename(), true) as $sub) {
                        $directories[] = $item->getFilename() . '/' . $sub;
                    }
                }
            }
        }

        return $directories;
    }

    public function makeDirectory(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        if (!is_dir($location)) {
            Log::info("Directory created: {$path}");
            return mkdir($location, 0755, true);
        }

        return false;
    }

    public function deleteDirectory(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        if (!is_dir($location)) {
            return false;
        }

        foreach (array_diff(scandir($location), ['.', '..']) as $item) {
            $itemPath = $location . '/' . $item;

            is_dir($itemPath)
                ? $this->deleteDirectory($path . '/' . $item)
                : unlink($itemPath);
        }

        Log::info("Directory deleted: {$path}");

        return rmdir($location);
    }

    /**
     * Resolve a relative path beneath the configured root. Paths are rejected
     * rather than rewritten when they contain traversal or absolute segments.
     * Resolving the nearest existing ancestor also blocks symlinks that point
     * outside the disk root.
     */
    protected function applyPathPrefix(string $path): string
    {
        $root = realpath($this->root);
        if ($root === false) {
            throw new FilesystemException("Root directory does not exist: {$this->root}");
        }

        $normalized = str_replace('\\', '/', $path);
        if (
            str_contains($normalized, "\0")
            || str_starts_with($normalized, '/')
            || preg_match('/^[A-Za-z]:\//', $normalized) === 1
        ) {
            throw new FilesystemException("Absolute or invalid path rejected: {$path}");
        }

        $segments = [];
        foreach (explode('/', $normalized) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            if ($segment === '..') {
                throw new FilesystemException("Path traversal detected: {$path}");
            }
            $segments[] = $segment;
        }

        $candidate = $root . ($segments === [] ? '' : DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $segments));
        $probe = $candidate;
        while (!file_exists($probe) && !is_link($probe)) {
            $parent = dirname($probe);
            if ($parent === $probe) {
                break;
            }
            $probe = $parent;
        }

        $resolvedProbe = realpath($probe);
        if ($resolvedProbe === false || !$this->isWithinRoot($resolvedProbe, $root)) {
            throw new FilesystemException("Path traversal detected: {$path} resolves outside the root directory.");
        }

        return $candidate;
    }

    private function isWithinRoot(string $path, string $root): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = strtolower($path);
            $root = strtolower($root);
        }

        return $path === $root || str_starts_with($path, $root . DIRECTORY_SEPARATOR);
    }

    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
