<?php

namespace App\Core\Filesystem\Drivers;

use App\Core\Filesystem\Exceptions\FilesystemException;
use App\Core\Support\Log;

class LocalDriver implements FilesystemDriver
{
    protected string $root;

    public function __construct(array $config)
    {
        $this->root = $config['root'];
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

    public function download(string $path, string $name = null, array $headers = [])
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

    public function files(string $directory = null, bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        $files = [];
        $items = new \FilesystemIterator($path);

        foreach ($items as $item) {
            if ($item->isFile()) {
                $files[] = $item->getPathname();
            } elseif ($recursive && $item->isDir()) {
                $files = array_merge($files, $this->files($item->getPathname(), true));
            }
        }

        return $files;
    }

    public function directories(string $directory = null, bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        $directories = [];
        $items = new \FilesystemIterator($path);

        foreach ($items as $item) {
            if ($item->isDir()) {
                $directories[] = $item->getPathname();
                if ($recursive) {
                    $directories = array_merge($directories, $this->directories($item->getPathname(), true));
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

        if (is_dir($location)) {
            Log::info("Directory deleted: {$path}");
            return rmdir($location);
        }

        return false;
    }

    protected function applyPathPrefix(string $path): string
    {
        return $this->root . '/' . ltrim($path, '/');
    }

    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }
}
