<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Drivers;

use App\Core\Filesystem\Exceptions\FilesystemException;
use App\Core\Support\Log;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Local filesystem driver.
 *
 * Stores files on the local disk beneath a configured root directory.
 * All path operations are sandboxed to prevent directory traversal attacks.
 * Uses {@see SplFileInfo} for reliable file metadata retrieval.
 */
class LocalDriver implements FilesystemDriver
{
    /** @var string The absolute root path for this disk. */
    protected string $root;

    /** @var int Directory permission mode for new directories. */
    protected int $directoryPermission;

    /** @var int File permission for 'public' visibility. */
    protected int $publicPermission;

    /** @var int File permission for 'private' visibility. */
    protected int $privatePermission;

    /**
     * Create a new local filesystem driver instance.
     *
     * @param array{root: string, permissions?: array{dir?: int, public?: int, private?: int}} $config
     *
     * @throws FilesystemException If the root directory configuration is missing.
     */
    public function __construct(array $config)
    {
        if (empty($config['root'])) {
            throw new FilesystemException('LocalDriver requires a "root" configuration value.');
        }

        $this->root = rtrim(
            str_replace('/', DIRECTORY_SEPARATOR, $config['root']),
            DIRECTORY_SEPARATOR
        );

        $permissions = $config['permissions'] ?? [];
        $this->directoryPermission = $permissions['dir'] ?? 0755;
        $this->publicPermission = $permissions['public'] ?? 0644;
        $this->privatePermission = $permissions['private'] ?? 0600;
    }

    /**
     * {@inheritDoc}
     */
    public function put(string $path, string $contents): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectoryExists(dirname($location));

        return file_put_contents($location, $contents) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $path): string
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile() || !$file->isReadable()) {
            throw new FilesystemException("File not found or not readable at path: {$path}");
        }

        $contents = file_get_contents($location);

        if ($contents === false) {
            throw new FilesystemException("Failed to read file at path: {$path}");
        }

        return $contents;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(string $path): bool
    {
        return file_exists($this->applyPathPrefix($path));
    }

    /**
     * {@inheritDoc}
     */
    public function delete(string $path): bool
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile()) {
            return false;
        }

        $result = unlink($location);

        if ($result) {
            Log::info("File deleted: {$path}");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function copy(string $from, string $to): bool
    {
        $fromLocation = $this->applyPathPrefix($from);
        $toLocation = $this->applyPathPrefix($to);

        $sourceFile = new SplFileInfo($fromLocation);

        if (!$sourceFile->isFile()) {
            throw new FilesystemException("Source file does not exist: {$from}");
        }

        $this->ensureDirectoryExists(dirname($toLocation));

        return copy($fromLocation, $toLocation);
    }

    /**
     * {@inheritDoc}
     */
    public function move(string $from, string $to): bool
    {
        $fromLocation = $this->applyPathPrefix($from);
        $toLocation = $this->applyPathPrefix($to);

        $sourceFile = new SplFileInfo($fromLocation);

        if (!$sourceFile->isFile()) {
            throw new FilesystemException("Source file does not exist: {$from}");
        }

        $this->ensureDirectoryExists(dirname($toLocation));

        $result = rename($fromLocation, $toLocation);

        if ($result) {
            Log::info("File moved: {$from} -> {$to}");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function size(string $path): int
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile()) {
            throw new FilesystemException("File not found at path: {$path}");
        }

        $size = $file->getSize();

        if ($size === false) {
            throw new FilesystemException("Unable to determine file size: {$path}");
        }

        return $size;
    }

    /**
     * {@inheritDoc}
     */
    public function lastModified(string $path): int
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile()) {
            throw new FilesystemException("File not found at path: {$path}");
        }

        $mtime = $file->getMTime();

        if ($mtime === false) {
            throw new FilesystemException("Unable to determine last modified time: {$path}");
        }

        return $mtime;
    }

    /**
     * {@inheritDoc}
     */
    public function mimeType(string $path): string
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile()) {
            throw new FilesystemException("File not found at path: {$path}");
        }

        $mime = mime_content_type($location);

        if ($mime === false) {
            throw new FilesystemException("Unable to determine MIME type: {$path}");
        }

        return $mime;
    }

    /**
     * {@inheritDoc}
     */
    public function url(string $path): string
    {
        return '/storage/' . ltrim(str_replace('\\', '/', $path), '/');
    }

    /**
     * {@inheritDoc}
     */
    public function download(string $path, ?string $name = null, array $headers = []): void
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile()) {
            throw new FilesystemException("File not found for download: {$path}");
        }

        $downloadName = $name ?? $file->getBasename();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . $file->getSize());

        foreach ($headers as $key => $value) {
            header("{$key}: {$value}");
        }

        readfile($location);
    }

    /**
     * {@inheritDoc}
     */
    public function files(string $directory = '', bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        if ($recursive) {
            return $this->listFilesRecursively($path, $directory);
        }

        return $this->listFilesInDirectory($path);
    }

    /**
     * {@inheritDoc}
     */
    public function directories(string $directory = '', bool $recursive = false): array
    {
        $path = $this->applyPathPrefix($directory);

        if (!is_dir($path)) {
            return [];
        }

        if ($recursive) {
            return $this->listDirectoriesRecursively($path, $directory);
        }

        return $this->listDirectoriesInDirectory($path);
    }

    /**
     * {@inheritDoc}
     */
    public function makeDirectory(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        if (is_dir($location)) {
            return false;
        }

        $result = mkdir($location, $this->directoryPermission, true);

        if ($result) {
            Log::info("Directory created: {$path}");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function deleteDirectory(string $path): bool
    {
        $location = $this->applyPathPrefix($path);

        if (!is_dir($location)) {
            return false;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($location, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }

        $result = rmdir($location);

        if ($result) {
            Log::info("Directory deleted: {$path}");
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function append(string $path, string $contents): bool
    {
        $location = $this->applyPathPrefix($path);
        $this->ensureDirectoryExists(dirname($location));

        return file_put_contents($location, $contents, FILE_APPEND) !== false;
    }

    /**
     * {@inheritDoc}
     */
    public function prepend(string $path, string $contents): bool
    {
        if ($this->exists($path)) {
            $existing = $this->get($path);
            return $this->put($path, $contents . $existing);
        }

        return $this->put($path, $contents);
    }

    /**
     * {@inheritDoc}
     */
    public function getVisibility(string $path): string
    {
        $location = $this->applyPathPrefix($path);
        $file = new SplFileInfo($location);

        if (!$file->isFile() && !$file->isDir()) {
            throw new FilesystemException("Path not found: {$path}");
        }

        $perms = $file->getPerms() & 0777;

        return ($perms & 0044) ? 'public' : 'private';
    }

    /**
     * {@inheritDoc}
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        $location = $this->applyPathPrefix($path);

        if (!file_exists($location)) {
            throw new FilesystemException("Path not found: {$path}");
        }

        $permission = match ($visibility) {
            'public' => $this->publicPermission,
            'private' => $this->privatePermission,
            default => throw new FilesystemException("Invalid visibility: {$visibility}. Must be 'public' or 'private'."),
        };

        return chmod($location, $permission);
    }

    /**
     * Resolve a relative path beneath the configured root.
     *
     * Paths are rejected (not rewritten) when they contain traversal or absolute
     * segments. Resolving the nearest existing ancestor also blocks symlinks that
     * point outside the disk root.
     *
     * @param string $path The relative path to resolve.
     *
     * @return string The absolute filesystem path.
     *
     * @throws FilesystemException If the path is invalid or escapes the root.
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

        // Validate that the resolved path stays within root
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

    /**
     * Determine if a resolved path is within the root directory.
     *
     * @param string $path The resolved path to check.
     * @param string $root The resolved root path.
     *
     * @return bool True if the path is within root.
     */
    private function isWithinRoot(string $path, string $root): bool
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            $path = strtolower($path);
            $root = strtolower($root);
        }

        return $path === $root || str_starts_with($path, $root . DIRECTORY_SEPARATOR);
    }

    /**
     * Ensure a directory exists, creating it recursively if needed.
     *
     * @param string $path Absolute directory path.
     *
     * @return void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, $this->directoryPermission, true);
        }
    }

    /**
     * List files in a single directory (non-recursive).
     *
     * @param string $path Absolute directory path.
     *
     * @return array<int, string> File names relative to the directory.
     */
    private function listFilesInDirectory(string $path): array
    {
        $files = [];
        $iterator = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $files[] = $item->getFilename();
            }
        }

        sort($files);

        return $files;
    }

    /**
     * List files recursively within a directory.
     *
     * @param string $absolutePath Absolute directory path.
     * @param string $relativePath Relative prefix for results.
     *
     * @return array<int, string> File paths relative to the disk root.
     */
    private function listFilesRecursively(string $absolutePath, string $relativePath): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absolutePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $filePath = str_replace(
                    [$absolutePath . DIRECTORY_SEPARATOR, '\\'],
                    ['', '/'],
                    $item->getPathname()
                );

                $files[] = $relativePath !== ''
                    ? rtrim($relativePath, '/') . '/' . $filePath
                    : $filePath;
            }
        }

        sort($files);

        return $files;
    }

    /**
     * List directories in a single directory (non-recursive).
     *
     * @param string $path Absolute directory path.
     *
     * @return array<int, string> Directory names.
     */
    private function listDirectoriesInDirectory(string $path): array
    {
        $directories = [];
        $iterator = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $directories[] = $item->getFilename();
            }
        }

        sort($directories);

        return $directories;
    }

    /**
     * List directories recursively.
     *
     * @param string $absolutePath Absolute directory path.
     * @param string $relativePath Relative prefix for results.
     *
     * @return array<int, string> Directory paths relative to the disk root.
     */
    private function listDirectoriesRecursively(string $absolutePath, string $relativePath): array
    {
        $directories = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($absolutePath, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        /** @var SplFileInfo $item */
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $dirPath = str_replace(
                    [$absolutePath . DIRECTORY_SEPARATOR, '\\'],
                    ['', '/'],
                    $item->getPathname()
                );

                $directories[] = $relativePath !== ''
                    ? rtrim($relativePath, '/') . '/' . $dirPath
                    : $dirPath;
            }
        }

        sort($directories);

        return $directories;
    }
}
