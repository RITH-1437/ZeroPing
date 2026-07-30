<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Validation\Validator as NewValidator;

/**
 * Backward-compatible validation wrapper.
 *
 * Delegates all validation logic to the newer App\Core\Validation\Validator.
 * Provides a simplified API for common validation workflows.
 *
 * @see NewValidator
 */
class Validator
{
    /**
     * The underlying validator instance.
     */
    protected NewValidator $validator;

    /**
     * Create a new Validator instance.
     *
     * @param array<string, mixed>    $data  The data to validate.
     * @param array<string, string[]> $rules The validation rules.
     */
    public function __construct(array $data = [], array $rules = [])
    {
        $this->validator = NewValidator::make($data, $rules);
    }

    /**
     * Validate the given data against the given rules.
     *
     * Creates a fresh validator instance and checks if validation passes.
     *
     * @param array<string, mixed>    $data  The data to validate.
     * @param array<string, string[]> $rules The validation rules.
     *
     * @return bool True if validation passes.
     */
    public function validate(array $data, array $rules): bool
    {
        $this->validator = NewValidator::make($data, $rules);

        return $this->validator->passes();
    }

    /**
     * Determine if the validation passes.
     *
     * @return bool True if all rules pass.
     */
    public function passes(): bool
    {
        return $this->validator->passes();
    }

    /**
     * Get the validation error messages.
     *
     * @return array<string, string[]> Errors keyed by field name.
     */
    public function errors(): array
    {
        return $this->validator->errors();
    }
}
