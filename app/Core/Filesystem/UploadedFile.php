<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

use App\Core\Filesystem\Exceptions\FilesystemException;

/**
 * Represents a file uploaded via an HTTP request.
 *
 * Wraps PHP's native `$_FILES` array entry with validation, safe naming,
 * and integration with the {@see Storage} facade for persisting uploads
 * to configured disks.
 *
 * Security considerations:
 * - The client-reported MIME type is never trusted; server-side detection is used.
 * - File names are sanitized to prevent directory traversal and null-byte injection.
 * - Upload validity is checked using `is_uploaded_file()` to prevent LFI attacks.
 * - Extension validation ensures only safe extensions are accepted.
 */
class UploadedFile
{
    /** @var string Original client-provided file name. */
    protected readonly string $name;

    /** @var string Client-reported MIME type (untrusted). */
    protected readonly string $clientMimeType;

    /** @var string Server temporary file path. */
    protected readonly string $tmpName;

    /** @var int PHP upload error code (UPLOAD_ERR_* constant). */
    protected readonly int $error;

    /** @var int File size in bytes. */
    protected readonly int $fileSize;

    /**
     * Maximum allowed file name length.
     */
    private const MAX_FILENAME_LENGTH = 255;

    /**
     * Create a new UploadedFile instance from a $_FILES entry.
     *
     * @param array{name?: string, type?: string, tmp_name?: string, error?: int, size?: int} $file
     *    A single entry from the PHP $_FILES superglobal.
     *
     * @throws FilesystemException If the file array structure is invalid.
     */
    public function __construct(array $file)
    {
        $this->name = (string) ($file['name'] ?? '');
        $this->clientMimeType = (string) ($file['type'] ?? '');
        $this->tmpName = (string) ($file['tmp_name'] ?? '');
        $this->error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        $this->fileSize = (int) ($file['size'] ?? 0);
    }

    /**
     * Move the uploaded file to a target directory.
     *
     * Uses `move_uploaded_file()` for secure transfer from the temp location.
     *
     * @param string      $directory The target directory (absolute path).
     * @param string|null $name      Optional custom filename. Defaults to original name.
     *
     * @return bool True if the file was moved successfully.
     *
     * @throws FilesystemException If validation fails or the move is not possible.
     */
    public function move(string $directory, ?string $name = null): bool
    {
        $this->validateUpload();

        $filename = $this->safeName($name ?? $this->name);
        if ($filename === '') {
            throw new FilesystemException('Invalid or empty file name after sanitization.');
        }

        if (!is_dir($directory)) {
            throw new FilesystemException("Target directory does not exist: {$directory}");
        }

        if (!is_writable($directory)) {
            throw new FilesystemException("Target directory is not writable: {$directory}");
        }

        $target = rtrim($directory, DIRECTORY_SEPARATOR . '/') . DIRECTORY_SEPARATOR . $filename;

        return move_uploaded_file($this->tmpName, $target);
    }

    /**
     * Store the uploaded file on a storage disk with a generated hash name.
     *
     * @param string      $path The relative directory path within the disk.
     * @param string|null $disk The storage disk name (null for default).
     *
     * @return string The relative path to the stored file.
     *
     * @throws FilesystemException If the upload is invalid or storage fails.
     */
    public function store(string $path, ?string $disk = null): string
    {
        return $this->storeAs($path, $this->hashName(), $disk);
    }

    /**
     * Store the uploaded file on a storage disk with a specific name.
     *
     * @param string      $path The relative directory path within the disk.
     * @param string      $name The filename to use.
     * @param string|null $disk The storage disk name (null for default).
     *
     * @return string The relative path to the stored file.
     *
     * @throws FilesystemException If the upload is invalid or storage fails.
     */
    public function storeAs(string $path, string $name, ?string $disk = null): string
    {
        $this->validateUpload();

        $name = $this->safeName($name);
        if ($name === '') {
            throw new FilesystemException('Invalid or empty file name after sanitization.');
        }

        $contents = file_get_contents($this->tmpName);
        if ($contents === false) {
            throw new FilesystemException('Unable to read uploaded file contents.');
        }

        $path = trim($path, '/\\');
        $fullPath = ($path === '' ? '' : $path . '/') . $name;

        $stored = Storage::disk($disk)->put($fullPath, $contents);
        if (!$stored) {
            throw new FilesystemException("Failed to store uploaded file at: {$fullPath}");
        }

        return $fullPath;
    }

    /**
     * Get the file extension from the original name.
     *
     * Only returns alphanumeric extensions up to 20 characters to prevent
     * injection via crafted filenames.
     *
     * @return string The lowercase file extension, or empty string if invalid.
     */
    public function extension(): string
    {
        $extension = strtolower(pathinfo(basename($this->name), PATHINFO_EXTENSION));

        return preg_match('/^[a-z0-9]{1,20}$/D', $extension) === 1 ? $extension : '';
    }

    /**
     * Get the original client-provided file name.
     *
     * @return string The original name (may contain unsafe characters — do not use directly in paths).
     */
    public function originalName(): string
    {
        return $this->name;
    }

    /**
     * Get the file size in bytes.
     *
     * @return int The file size reported by PHP.
     */
    public function size(): int
    {
        return $this->fileSize;
    }

    /**
     * Get the MIME type detected by the server.
     *
     * Never trusts the client-reported MIME type. Falls back to the client
     * type only if server detection is unavailable.
     *
     * @return string The detected MIME type.
     */
    public function mimeType(): string
    {
        if ($this->tmpName !== '' && is_file($this->tmpName) && function_exists('mime_content_type')) {
            $detected = mime_content_type($this->tmpName);
            if ($detected !== false) {
                return $detected;
            }
        }

        return $this->clientMimeType;
    }

    /**
     * Determine if the uploaded file is valid.
     *
     * A file is valid if:
     * - No upload error occurred (UPLOAD_ERR_OK)
     * - The temp file path is non-empty
     * - PHP confirms it was actually uploaded (is_uploaded_file)
     *
     * @return bool True if the upload is valid.
     */
    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK
            && $this->tmpName !== ''
            && is_uploaded_file($this->tmpName);
    }

    /**
     * Get a human-readable upload error message.
     *
     * @return string The error message, or empty string if no error.
     */
    public function getErrorMessage(): string
    {
        return match ($this->error) {
            UPLOAD_ERR_OK => '',
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder for uploads.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.',
            default => 'Unknown upload error.',
        };
    }

    /**
     * Get the PHP upload error code.
     *
     * @return int One of the UPLOAD_ERR_* constants.
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * Validate that the file extension is one of the allowed types.
     *
     * @param array<int, string> $allowedExtensions List of allowed extensions (without dots).
     *
     * @return bool True if the extension is allowed.
     */
    public function hasAllowedExtension(array $allowedExtensions): bool
    {
        $ext = $this->extension();

        if ($ext === '') {
            return false;
        }

        return in_array($ext, array_map('strtolower', $allowedExtensions), true);
    }

    /**
     * Validate that the file MIME type is one of the allowed types.
     *
     * @param array<int, string> $allowedMimeTypes List of allowed MIME types.
     *
     * @return bool True if the MIME type is allowed.
     */
    public function hasAllowedMimeType(array $allowedMimeTypes): bool
    {
        return in_array($this->mimeType(), $allowedMimeTypes, true);
    }

    /**
     * Validate that the file size does not exceed a maximum.
     *
     * @param int $maxBytes Maximum file size in bytes.
     *
     * @return bool True if the file is within the size limit.
     */
    public function isWithinSizeLimit(int $maxBytes): bool
    {
        return $this->fileSize <= $maxBytes;
    }

    /**
     * Generate a hash-based unique file name with the original extension.
     *
     * @return string A unique filename (e.g. "a3f4b2c1d8...ext").
     */
    protected function hashName(): string
    {
        $extension = $this->extension();

        return bin2hex(random_bytes(20)) . ($extension === '' ? '' : '.' . $extension);
    }

    /**
     * Sanitize a file name to prevent directory traversal and injection attacks.
     *
     * Removes path separators, null bytes, control characters, and limits length.
     *
     * @param string $name The raw file name to sanitize.
     *
     * @return string The sanitized file name, or empty string if completely invalid.
     */
    private function safeName(string $name): string
    {
        // Extract just the basename (no path components)
        $name = basename(str_replace('\\', '/', $name));

        // Remove null bytes and control characters
        $name = str_replace(["\0", "\r", "\n"], '', $name);
        $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name) ?? '';

        // Reject names that are too long
        if (strlen($name) > self::MAX_FILENAME_LENGTH) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            $base = pathinfo($name, PATHINFO_FILENAME);
            $maxBase = self::MAX_FILENAME_LENGTH - strlen($extension) - 1;
            $name = substr($base, 0, max(1, $maxBase)) . '.' . $extension;
        }

        // Reject dot-only names (. and ..)
        if ($name === '.' || $name === '..') {
            return '';
        }

        return $name;
    }

    /**
     * Validate the upload, throwing an exception if invalid.
     *
     * @return void
     *
     * @throws FilesystemException If the upload is not valid.
     */
    private function validateUpload(): void
    {
        if ($this->error !== UPLOAD_ERR_OK) {
            throw new FilesystemException(
                "Upload failed: {$this->getErrorMessage()} (error code: {$this->error})"
            );
        }

        if ($this->tmpName === '') {
            throw new FilesystemException('Upload failed: no temporary file path provided.');
        }

        if (!is_uploaded_file($this->tmpName)) {
            throw new FilesystemException(
                'Upload validation failed: file was not uploaded via HTTP POST.'
            );
        }
    }
}
