<?php

declare(strict_types=1);

namespace App\Core\Filesystem;

class UploadedFile
{
    protected string $name;
    protected string $type;
    protected string $tmpName;
    protected int $error;
    protected int $size;

    public function __construct(array $file)
    {
        $this->name = (string) ($file['name'] ?? '');
        $this->type = (string) ($file['type'] ?? '');
        $this->tmpName = (string) ($file['tmp_name'] ?? '');
        $this->error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        $this->size = (int) ($file['size'] ?? 0);
    }

    public function move(string $directory, ?string $name = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $name = $this->safeName($name ?? $this->name);
        if ($name === '') {
            return false;
        }

        $target = rtrim($directory, DIRECTORY_SEPARATOR . '/');
        return move_uploaded_file($this->tmpName, $target . DIRECTORY_SEPARATOR . $name);
    }

    public function store(string $path, ?string $disk = null): string|false
    {
        return $this->storeAs($path, $this->hashName(), $disk);
    }

    public function storeAs(string $path, string $name, ?string $disk = null): string|false
    {
        if (!$this->isValid()) {
            return false;
        }

        $name = $this->safeName($name);
        if ($name === '') {
            return false;
        }

        $contents = file_get_contents($this->tmpName);
        if ($contents === false) {
            return false;
        }

        $path = trim($path, '/\\');
        if (!storage($disk)->put($path . '/' . $name, $contents)) {
            return false;
        }

        return ($path === '' ? '' : $path . '/') . $name;
    }

    public function extension(): string
    {
        $extension = strtolower(pathinfo(basename($this->name), PATHINFO_EXTENSION));
        return preg_match('/^[a-z0-9]{1,20}$/D', $extension) === 1 ? $extension : '';
    }

    public function originalName(): string
    {
        return $this->name;
    }

    public function size(): int
    {
        return $this->size;
    }

    /** Return server-detected MIME type when available, never trust the client header. */
    public function mimeType(): string
    {
        if ($this->tmpName !== '' && is_file($this->tmpName) && function_exists('mime_content_type')) {
            return (string) mime_content_type($this->tmpName);
        }

        return $this->type;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK
            && $this->tmpName !== ''
            && is_uploaded_file($this->tmpName);
    }

    protected function hashName(): string
    {
        $extension = $this->extension();
        return bin2hex(random_bytes(20)) . ($extension === '' ? '' : '.' . $extension);
    }

    private function safeName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = str_replace(["\0", "\r", "\n"], '', $name);

        return $name;
    }
}
