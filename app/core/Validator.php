<?php

namespace App\Core;
class Validator
{
    private array $errors = [];

    public function required(string $field, $value): self
    {
        if (trim($value) === '') {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }

        return $this;
    }

    public function email(string $field, $value): self
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Invalid email address.';
        }

        return $this;
    }

    public function min(string $field, $value, int $length): self
    {
        if (strlen($value) < $length) {
            $this->errors[$field] = ucfirst($field) . " must be at least {$length} characters.";
        }

        return $this;
    }

    public function max(string $field, $value, int $length): self
    {
        if (strlen($value) > $length) {
            $this->errors[$field] = ucfirst($field) . " may not exceed {$length} characters.";
        }

        return $this;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }
}