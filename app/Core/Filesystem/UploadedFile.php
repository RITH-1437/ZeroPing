<?php

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
        $this->name = $file['name'];
        $this->type = $file['type'];
        $this->tmpName = $file['tmp_name'];
        $this->error = $file['error'];
        $this->size = $file['size'];
    }

    public function move(string $directory, string $name = null): bool
    {
        $name = $name ?: $this->name;
        $path = $directory . '/' . $name;

        return move_uploaded_file($this->tmpName, $path);
    }

    public function store(string $path, string $disk = null): string|false
    {
        $name = $this->hashName();

        return $this->storeAs($path, $name, $disk);
    }

    public function storeAs(string $path, string $name, string $disk = null): string|false
    {
        $disk = storage($disk);

        $path = rtrim($path, '/');

        if (!$disk->put($path . '/' . $name, file_get_contents($this->tmpName))) {
            return false;
        }

        return $path . '/' . $name;
    }

    public function extension(): string
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    public function originalName(): string
    {
        return $this->name;
    }

    public function size(): int
    {
        return $this->size;
    }

    public function mimeType(): string
    {
        return $this->type;
    }

    public function isValid(): bool
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    protected function hashName(): string
    {
        return md5_file($this->tmpName) . '.' . $this->extension();
    }
}
