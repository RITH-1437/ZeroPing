<?php

namespace App\Core\Validation;

class ValidationResult
{
    protected array $errors = [];

    public function add(
        string $field,
        string $message
    ): void {

        $this->errors[$field][] = $message;
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }
}