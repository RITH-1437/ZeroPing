<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Filesystem\Drivers\FilesystemDriver;

/**
 * Filesystem repository (disk adapter).
 *
 * Acts as a thin, type-safe wrapper around a {@see FilesystemDriver} implementation.
 * This class is what consumers interact with when they call `Storage::disk('local')`.
 *
 * All methods delegate to the underlying driver, providing a stable public API
 * that is decoupled from driver-specific implementation details.
 */
class FilesystemRepository
{
    /**
     * The underlying filesystem driver.
     *
     * @var FilesystemDriver
     */
    protected FilesystemDriver $driver;

    /**
     * Create a new filesystem repository instance.
     *
     * @param FilesystemDriver $driver The driver to delegate operations to.
     */
    public function __construct(FilesystemDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Write contents to a file.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The file contents to write.
     *
     * @return bool True on success.
     */
    public function put(string $path, string $contents): bool
    {
        return $this->driver->put($path, $contents);
    }

    /**
     * Read the contents of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string The file contents.
     */
    public function get(string $path): string
    {
        return $this->driver->get($path);
    }

    /**
     * Determine if a file or directory exists.
     *
     * @param string $path Relative path within the disk.
     *
     * @return bool True if the path exists.
     */
    public function exists(string $path): bool
    {
        return $this->driver->exists($path);
    }

    /**
     * Delete a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return bool True if deleted.
     */
    public function delete(string $path): bool
    {
        return $this->driver->delete($path);
    }

    /**
     * Copy a file from one location to another.
     *
     * @param string $from Source path.
     * @param string $to   Destination path.
     *
     * @return bool True on success.
     */
    public function copy(string $from, string $to): bool
    {
        return $this->driver->copy($from, $to);
    }

    /**
     * Move a file from one location to another.
     *
     * @param string $from Source path.
     * @param string $to   Destination path.
     *
     * @return bool True on success.
     */
    public function move(string $from, string $to): bool
    {
        return $this->driver->move($from, $to);
    }

    /**
     * Get the file size in bytes.
     *
     * @param string $path Relative path within the disk.
     *
     * @return int File size in bytes.
     */
    public function size(string $path): int
    {
        return $this->driver->size($path);
    }

    /**
     * Get the last modification timestamp.
     *
     * @param string $path Relative path within the disk.
     *
     * @return int Unix timestamp.
     */
    public function lastModified(string $path): int
    {
        return $this->driver->lastModified($path);
    }

    /**
     * Get the MIME type of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string MIME type string.
     */
    public function mimeType(string $path): string
    {
        return $this->driver->mimeType($path);
    }

    /**
     * Get the public URL for a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string The public URL.
     */
    public function url(string $path): string
    {
        return $this->driver->url($path);
    }

    /**
     * Stream a file as a download response.
     *
     * @param string              $path    Relative path within the disk.
     * @param string|null         $name    Custom download filename.
     * @param array<string, string> $headers Additional HTTP headers.
     *
     * @return void
     */
    public function download(string $path, ?string $name = null, array $headers = []): void
    {
        $this->driver->download($path, $name, $headers);
    }

    /**
     * List all files in a directory.
     *
     * @param string $directory Relative directory path.
     * @param bool   $recursive Whether to include files in subdirectories.
     *
     * @return array<int, string> List of relative file paths.
     */
    public function files(string $directory = '', bool $recursive = false): array
    {
        return $this->driver->files($directory, $recursive);
    }

    /**
     * List all subdirectories in a directory.
     *
     * @param string $directory Relative directory path.
     * @param bool   $recursive Whether to include nested subdirectories.
     *
     * @return array<int, string> List of relative directory paths.
     */
    public function directories(string $directory = '', bool $recursive = false): array
    {
        return $this->driver->directories($directory, $recursive);
    }

    /**
     * Create a directory.
     *
     * @param string $path Relative path for the new directory.
     *
     * @return bool True if created.
     */
    public function makeDirectory(string $path): bool
    {
        return $this->driver->makeDirectory($path);
    }

    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $path Relative path of the directory to delete.
     *
     * @return bool True on success.
     */
    public function deleteDirectory(string $path): bool
    {
        return $this->driver->deleteDirectory($path);
    }

    /**
     * Append contents to a file.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The content to append.
     *
     * @return bool True on success.
     */
    public function append(string $path, string $contents): bool
    {
        return $this->driver->append($path, $contents);
    }

    /**
     * Prepend contents to a file.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The content to prepend.
     *
     * @return bool True on success.
     */
    public function prepend(string $path, string $contents): bool
    {
        return $this->driver->prepend($path, $contents);
    }

    /**
     * Get the visibility (permissions) of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string 'public' or 'private'.
     */
    public function getVisibility(string $path): string
    {
        return $this->driver->getVisibility($path);
    }

    /**
     * Set the visibility (permissions) of a file.
     *
     * @param string $path       Relative path within the disk.
     * @param string $visibility 'public' or 'private'.
     *
     * @return bool True on success.
     */
    public function setVisibility(string $path, string $visibility): bool
    {
        return $this->driver->setVisibility($path, $visibility);
    }

    /**
     * Get the underlying driver instance.
     *
     * @return FilesystemDriver The driver backing this repository.
     */
    public function getDriver(): FilesystemDriver
    {
        return $this->driver;
    }
}
