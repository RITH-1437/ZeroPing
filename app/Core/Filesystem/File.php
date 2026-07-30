<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Filesystem\Exceptions\FilesystemException;
use SplFileInfo;

/**
 * Convenience wrapper around a single file path.
 *
 * Provides a fluent, object-oriented API for common file operations on
 * a specific path. Internally delegates to {@see SplFileInfo} for metadata
 * where appropriate.
 */
class File
{
    /** @var string The absolute or relative file path. */
    protected string $path;

    /** @var SplFileInfo Cached SplFileInfo instance for metadata. */
    protected SplFileInfo $info;

    /**
     * Create a new File instance.
     *
     * @param string $path The file path (absolute or relative to CWD).
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->info = new SplFileInfo($path);
    }

    /**
     * Determine if the file exists on disk.
     *
     * @return bool True if the file exists.
     */
    public function exists(): bool
    {
        return $this->info->isFile();
    }

    /**
     * Read the entire file contents.
     *
     * @return string The file contents.
     *
     * @throws FilesystemException If the file does not exist or cannot be read.
     */
    public function get(): string
    {
        if (!$this->exists()) {
            throw new FilesystemException("File does not exist: {$this->path}");
        }

        $contents = file_get_contents($this->path);

        if ($contents === false) {
            throw new FilesystemException("Unable to read file: {$this->path}");
        }

        return $contents;
    }

    /**
     * Write contents to the file, overwriting any existing content.
     *
     * @param string $contents The content to write.
     *
     * @return bool True on success.
     */
    public function put(string $contents): bool
    {
        return file_put_contents($this->path, $contents) !== false;
    }

    /**
     * Append contents to the end of the file.
     *
     * @param string $contents The content to append.
     *
     * @return bool True on success.
     */
    public function append(string $contents): bool
    {
        return file_put_contents($this->path, $contents, FILE_APPEND) !== false;
    }

    /**
     * Delete the file from disk.
     *
     * @return bool True if the file was deleted, false if it did not exist.
     *
     * @throws FilesystemException If the file exists but cannot be deleted.
     */
    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        }

        $result = unlink($this->path);

        if (!$result) {
            throw new FilesystemException("Unable to delete file: {$this->path}");
        }

        return true;
    }

    /**
     * Get the file size in bytes.
     *
     * @return int File size in bytes.
     *
     * @throws FilesystemException If the file does not exist.
     */
    public function size(): int
    {
        if (!$this->exists()) {
            throw new FilesystemException("File does not exist: {$this->path}");
        }

        $size = $this->info->getSize();

        if ($size === false) {
            throw new FilesystemException("Unable to determine file size: {$this->path}");
        }

        return $size;
    }

    /**
     * Get the last modification time as a Unix timestamp.
     *
     * @return int Unix timestamp of last modification.
     *
     * @throws FilesystemException If the file does not exist.
     */
    public function lastModified(): int
    {
        if (!$this->exists()) {
            throw new FilesystemException("File does not exist: {$this->path}");
        }

        $mtime = $this->info->getMTime();

        if ($mtime === false) {
            throw new FilesystemException("Unable to determine modification time: {$this->path}");
        }

        return $mtime;
    }

    /**
     * Get the MIME content type of the file.
     *
     * @return string The MIME type (e.g. "text/plain").
     *
     * @throws FilesystemException If the file does not exist or MIME detection fails.
     */
    public function mimeType(): string
    {
        if (!$this->exists()) {
            throw new FilesystemException("File does not exist: {$this->path}");
        }

        $mime = mime_content_type($this->path);

        if ($mime === false) {
            throw new FilesystemException("Unable to determine MIME type: {$this->path}");
        }

        return $mime;
    }

    /**
     * Get the file extension (without leading dot).
     *
     * @return string The file extension, or empty string if none.
     */
    public function extension(): string
    {
        return $this->info->getExtension();
    }

    /**
     * Get the filename without extension.
     *
     * @return string The file name without extension.
     */
    public function name(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    /**
     * Get the full basename (filename with extension).
     *
     * @return string The file basename.
     */
    public function basename(): string
    {
        return $this->info->getBasename();
    }

    /**
     * Get the parent directory path.
     *
     * @return string The directory containing the file.
     */
    public function dirname(): string
    {
        return $this->info->getPath();
    }

    /**
     * Get the underlying file path.
     *
     * @return string The original path provided to the constructor.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get the real (resolved) path of the file.
     *
     * @return string|false The resolved path or false if the file doesn't exist.
     */
    public function realPath(): string|false
    {
        return $this->info->getRealPath();
    }

    /**
     * Determine if the file is readable.
     *
     * @return bool True if the file is readable.
     */
    public function isReadable(): bool
    {
        return $this->info->isReadable();
    }

    /**
     * Determine if the file is writable.
     *
     * @return bool True if the file is writable.
     */
    public function isWritable(): bool
    {
        return $this->info->isWritable();
    }
}
