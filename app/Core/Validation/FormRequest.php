<?php

declare(strict_types=1);

namespace App\Core\Validation;

/**
 * Base class for form request validation.
 *
 * Encapsulates validation logic for a specific form/endpoint.
 * Subclasses define their rules and optional authorization logic.
 * Provides access to validated data and error messages.
 *
 * @example
 * class StoreUserRequest extends FormRequest
 * {
 *     public function rules(): array
 *     {
 *         return [
 *             'name' => 'required|string|max:255',
 *             'email' => 'required|email|unique:users',
 *         ];
 *     }
 * }
 *
 * @since 1.0.0
 * @author Rin Nairith
 * @link https://zero-ping.duckdns.org/docs/validation
 */
abstract class FormRequest
{
    /**
     * The input data to validate.
     *
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * Cached validation result to avoid repeated validation.
     */
    protected ?ValidationResult $validationResult = null;

    /**
     * Create a new form request instance.
     *
     * @param array<string, mixed>|null $data The input data. Defaults to merged GET and POST data.
     */
    public function __construct(?array $data = null)
    {
        $this->data = $data ?? array_merge($_GET, $_POST);
    }

    /**
     * Define the validation rules for this request.
     *
     * @return array<string, string> The rules keyed by field name.
     */
    abstract public function rules(): array;

    /**
     * Define custom validation messages.
     *
     * Override this method to provide custom error messages
     * using the "field.rule" notation.
     *
     * @return array<string, string> Custom messages keyed by "field.rule".
     */
    public function messages(): array
    {
        return [];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * Override this method to implement authorization checks.
     * Return false to deny the request before validation runs.
     *
     * @return bool True if authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validated data (only fields with defined rules).
     *
     * @return array<string, mixed> The validated data subset.
     *
     * @throws ValidationException If validation fails.
     */
    public function validated(): array
    {
        $this->runValidation();

        if ($this->validationResult === null) {
            return [];
        }

        if ($this->validationResult->fails()) {
            throw new ValidationException($this->validationResult->errors());
        }

        $validated = [];
        foreach (array_keys($this->rules()) as $field) {
            if (array_key_exists($field, $this->data)) {
                $validated[$field] = $this->data[$field];
            }
        }

        return $validated;
    }

    /**
     * Execute validation and return the result.
     *
     * @return ValidationResult The validation result.
     */
    public function validate(): ValidationResult
    {
        $this->runValidation();

        if ($this->validationResult === null) {
            $this->validationResult = new ValidationResult();
        }

        return $this->validationResult;
    }

    /**
     * Determine if validation passes.
     *
     * @return bool True if there are no validation errors.
     */
    public function passes(): bool
    {
        return $this->validate()->passes();
    }

    /**
     * Determine if validation fails.
     *
     * @return bool True if there are validation errors.
     */
    public function fails(): bool
    {
        return $this->validate()->fails();
    }

    /**
     * Get all validation errors.
     *
     * @return array<string, string[]> The validation errors.
     */
    public function errors(): array
    {
        return $this->validate()->errors();
    }

    /**
     * Get a specific input value.
     *
     * @param string $key     The input key.
     * @param mixed  $default Default value if key is not present.
     *
     * @return mixed The input value or default.
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Get all input data.
     *
     * @return array<string, mixed> The full input data array.
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Determine if the input contains a specific key.
     *
     * @param string $key The key to check for.
     *
     * @return bool True if the key exists in the data.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a subset of the input data.
     *
     * @param string[] $keys The keys to include.
     *
     * @return array<string, mixed> The subset of input data.
     */
    public function only(array $keys): array
    {
        return array_intersect_key($this->data, array_flip($keys));
    }

    /**
     * Run validation if not already performed.
     *
     * @return void
     */
    protected function runValidation(): void
    {
        if ($this->validationResult !== null) {
            return;
        }

        $this->validationResult = Validator::make(
            $this->data,
            $this->rules(),
            $this->messages()
        )->validate();
    }
}
