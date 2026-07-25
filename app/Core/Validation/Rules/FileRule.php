<?php
declare(strict_types=1);

namespace App\Core\Validation\Rules;

class FileRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        if (!is_array($value) || !isset($value['tmp_name']) || !isset($value['error'])) {
            return false;
        }

        if ($value['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        return is_uploaded_file($value['tmp_name']);
    }

    public function message(string $field, array $parameters = []): string
    {
        return "{$field} must be a valid uploaded file.";
    }
}
