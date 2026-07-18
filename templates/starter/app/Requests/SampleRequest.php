<?php

namespace App\Http\Requests;

use App\Core\Validation\FormRequest;

/**
 * SampleRequest — illustrative form request.
 *
 * Form requests centralise validation + authorization for an action. Return
 * your rules in rules() and gate access in authorize(). Inject the request
 * into a controller method to validate automatically.
 */
class SampleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body'  => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
        ];
    }
}
