<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Meta-rule indicating a field may be null or empty.
 *
 * This rule always passes validation. Its presence in the rule string
 * signals the Validator to skip all subsequent rules for the field
 * when the value is null or empty. It does not perform validation itself.
 */
class NullableRule extends AbstractRule
{
    /**
     * {@inheritDoc}
     *
     * Always returns true — nullable is handled as a meta-rule by the Validator.
     */
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {
        return true;
    }

    /**
     * {@inheritDoc}
     *
     * Returns an empty string since this rule never fails.
     */
    public function message(string $field, array $parameters = []): string
    {
        return '';
    }
}
