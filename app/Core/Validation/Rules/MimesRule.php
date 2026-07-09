<?php

namespace App\Core\Validation\Rules;

class MimesRule extends FileRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if (!parent::validate($field, $value, $data, $parameters)) {
            return false;
        }

        if ($this->isEmpty($value)) {
            return true;
        }

        if (empty($parameters)) {
            return true;
        }

        $extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

        return in_array($extension, $parameters, true);
    }

    public function message(string $field, array $parameters = []): string
    {
        $allowed = implode(', ', $parameters);
        return "{$field} must be a file of type: {$allowed}.";
    }
}
