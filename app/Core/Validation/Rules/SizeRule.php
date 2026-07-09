<?php

namespace App\Core\Validation\Rules;

class SizeRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        $maxKb = (int) ($parameters[0] ?? 0);
        if ($maxKb <= 0) {
            return true;
        }

        if (is_array($value) && isset($value['tmp_name'])) {
            $sizeKb = filesize($value['tmp_name']) / 1024;
        } elseif (is_string($value)) {
            $sizeKb = strlen($value) / 1024;
        } else {
            return false;
        }

        return $sizeKb <= $maxKb;
    }

    public function message(string $field, array $parameters = []): string
    {
        $max = $parameters[0] ?? '0';
        return "{$field} must not exceed {$max}KB.";
    }
}
