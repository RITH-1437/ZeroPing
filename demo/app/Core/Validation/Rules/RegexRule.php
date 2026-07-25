<?php

namespace App\Core\Validation\Rules;

class RegexRule extends AbstractRule
{
    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if ($this->isEmpty($value)) {
            return true;
        }

        if (empty($parameters)) {
            return true;
        }

        $pattern = $parameters[0];

        return (bool) preg_match($pattern, (string) $value);
    }

    public function message(string $field, array $parameters = []): string
    {
        return "{$field} format is invalid.";
    }
}
