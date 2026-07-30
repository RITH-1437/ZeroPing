<?php

declare(strict_types=1);

namespace App\Core\Filesystem\Drivers;

use App\Core\Filesystem\Exceptions\FilesystemException;

/**
 * Contract for all filesystem drivers.
 *
 * Each driver adapts a specific storage backend (local disk, S3, FTP, etc.)
 * to a unified interface consumed by {@see \App\Core\Filesystem\FilesystemRepository}.
 *
 * Implementations MUST throw {@see FilesystemException} on unrecoverable I/O
 * errors rather than returning ambiguous values.
 */
interface FilesystemDriver
{
    /**
     * Write contents to a file path.
     *
     * Parent directories should be created automatically when the driver
     * supports it.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The file contents to write.
     *
     * @return bool True on success, false on failure.
     *
     * @throws FilesystemException If the path is invalid or a write error occurs.
     */
    public function put(string $path, string $contents): bool;

    /**
     * Read the contents of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string The file contents.
     *
     * @throws FilesystemException If the file does not exist or cannot be read.
     */
    public function get(string $path): string;

    /**
     * Determine if a file or directory exists at the given path.
     *
     * @param string $path Relative path within the disk.
     *
     * @return bool True if the path exists, false otherwise.
     */
    public function exists(string $path): bool;

    /**
     * Delete a file at the given path.
     *
     * @param string $path Relative path within the disk.
     *
     * @return bool True if the file was deleted, false if it did not exist.
     *
     * @throws FilesystemException If deletion fails for a reason other than non-existence.
     */
    public function delete(string $path): bool;

    /**
     * Copy a file from one location to another.
     *
     * @param string $from Source path (relative to disk root).
     * @param string $to   Destination path (relative to disk root).
     *
     * @return bool True on success.
     *
     * @throws FilesystemException If the source does not exist or copy fails.
     */
    public function copy(string $from, string $to): bool;

    /**
     * Move (rename) a file from one location to another.
     *
     * @param string $from Source path (relative to disk root).
     * @param string $to   Destination path (relative to disk root).
     *
     * @return bool True on success.
     *
     * @throws FilesystemException If the source does not exist or move fails.
     */
    public function move(string $from, string $to): bool;

    /**
     * Get the file size in bytes.
     *
     * @param string $path Relative path within the disk.
     *
     * @return int File size in bytes.
     *
     * @throws FilesystemException If the file does not exist.
     */
    public function size(string $path): int;

    /**
     * Get the last modification timestamp (Unix epoch seconds).
     *
     * @param string $path Relative path within the disk.
     *
     * @return int Unix timestamp of last modification.
     *
     * @throws FilesystemException If the file does not exist.
     */
    public function lastModified(string $path): int;

    /**
     * Get the MIME type of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string MIME type string (e.g. "text/plain").
     *
     * @throws FilesystemException If the file does not exist or MIME detection fails.
     */
    public function mimeType(string $path): string;

    /**
     * Get the public URL for a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string The publicly accessible URL.
     */
    public function url(string $path): string;

    /**
     * Stream a file as a download response.
     *
     * @param string      $path    Relative path within the disk.
     * @param string|null $name    Optional filename for the Content-Disposition header.
     * @param array<string, string> $headers Additional HTTP headers.
     *
     * @return void
     *
     * @throws FilesystemException If the file does not exist.
     */
    public function download(string $path, ?string $name = null, array $headers = []): void;

    /**
     * List all files in a directory.
     *
     * @param string $directory Relative directory path (empty string for root).
     * @param bool   $recursive Whether to list files in subdirectories.
     *
     * @return array<int, string> List of relative file paths.
     */
    public function files(string $directory = '', bool $recursive = false): array;

    /**
     * List all subdirectories in a directory.
     *
     * @param string $directory Relative directory path (empty string for root).
     * @param bool   $recursive Whether to list nested subdirectories.
     *
     * @return array<int, string> List of relative directory paths.
     */
    public function directories(string $directory = '', bool $recursive = false): array;

    /**
     * Create a directory.
     *
     * @param string $path Relative path for the new directory.
     *
     * @return bool True if created, false if it already exists.
     */
    public function makeDirectory(string $path): bool;

    /**
     * Recursively delete a directory and its contents.
     *
     * @param string $path Relative path of the directory to delete.
     *
     * @return bool True on success, false if the directory does not exist.
     */
    public function deleteDirectory(string $path): bool;

    /**
     * Append contents to a file.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The content to append.
     *
     * @return bool True on success.
     *
     * @throws FilesystemException If the append fails.
     */
    public function append(string $path, string $contents): bool;

    /**
     * Prepend contents to a file.
     *
     * @param string $path     Relative path within the disk.
     * @param string $contents The content to prepend.
     *
     * @return bool True on success.
     *
     * @throws FilesystemException If the operation fails.
     */
    public function prepend(string $path, string $contents): bool;

    /**
     * Get the visibility (permissions) of a file.
     *
     * @param string $path Relative path within the disk.
     *
     * @return string 'public' or 'private'.
     */
    public function getVisibility(string $path): string;

    /**
     * Set the visibility (permissions) of a file.
     *
     * @param string $path       Relative path within the disk.
     * @param string $visibility 'public' or 'private'.
     *
     * @return bool True on success.
     */
    public function setVisibility(string $path, string $visibility): bool;
}
