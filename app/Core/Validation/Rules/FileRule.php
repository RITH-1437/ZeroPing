<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field is a valid uploaded file.
 *
 * Checks that the value is a PHP file upload array with a valid
 * tmp_name, no upload error, and that it was actually uploaded
 * via HTTP POST (is_uploaded_file check).
 */
class FileRule extends AbstractRule
{
    /**
     * {@inheritDoc}
     */
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {
        if ($this->isEmpty($value)) {
            return true;
        }

        if (!$this->isValidUploadArray($value)) {
            return false;
        }

        if ($value['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        return is_uploaded_file($value['tmp_name']);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field must be a valid uploaded file.";
    }

    /**
     * Determine if the value has the structure of a PHP file upload array.
     *
     * @param mixed $value The value to check.
     *
     * @return bool True if the value has tmp_name and error keys.
     */
    protected function isValidUploadArray(mixed $value): bool
    {
        return is_array($value)
            && isset($value['tmp_name'])
            && isset($value['error']);
    }
}
