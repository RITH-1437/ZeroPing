<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a file or string does not exceed a maximum size in kilobytes.
 *
 * For file uploads, checks the actual file size on disk.
 * For strings, checks the byte length.
 * The parameter specifies the maximum allowed size in KB.
 *
 * @example "size:2048" — file must be <= 2MB
 */
class SizeRule extends AbstractRule
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

        $maxKb = (int) $this->parameter($parameters, 0, 0);

        if ($maxKb <= 0) {
            return true;
        }

        $sizeKb = $this->getSizeInKb($value);

        if ($sizeKb === null) {
            return false;
        }

        return $sizeKb <= $maxKb;
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        $max = $this->parameter($parameters, 0, '0');

        return "The {$this->formatFieldName($field)} field must not exceed {$max}KB.";
    }

    /**
     * Calculate the size of the value in kilobytes.
     *
     * @param mixed $value The value to measure.
     *
     * @return float|null The size in KB, or null if the type is unsupported.
     */
    private function getSizeInKb(mixed $value): ?float
    {
        // File upload array
        if (is_array($value) && isset($value['tmp_name']) && is_string($value['tmp_name'])) {
            $fileSize = @filesize($value['tmp_name']);
            if ($fileSize === false) {
                return null;
            }
            return $fileSize / 1024;
        }

        // String value
        if (is_string($value)) {
            return strlen($value) / 1024;
        }

        return null;
    }
}
