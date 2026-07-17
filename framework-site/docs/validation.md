# Validation

ZeroPing provides a flexible validation system with support for complex rule chains, file uploads, custom messages, and both fluent and declarative APIs.

## Basic Usage

```php
$validator = Validator::make($data, [
    'name'  => 'required|min:2|max:255',
    'email' => 'required|email|unique:users,email',
    'age'   => 'required|integer|min:18',
]);

if ($validator->fails()) {
    $errors = $validator->errors();
}
```

## Available Rules

### Core Rules

| Rule | Description |
|---|---|
| `required` | Field must not be empty |
| `nullable` | Skip validation if field is empty |
| `string` | Value must be a string |
| `integer` | Value must be an integer |
| `numeric` | Value must be numeric |
| `email` | Value must be a valid email |
| `min:N` | Minimum length/value of N |
| `max:N` | Maximum length/value of N |
| `between:N,M` | Value must be between N and M |
| `same:field` | Value must match another field |
| `confirmed` | Value must match `field_confirmation` |

### Database Rules

| Rule | Description |
|---|---|
| `unique:table,column,except,idColumn` | Value must be unique in the database |
| `exists:table,column` | Value must exist in the database |

### File Rules

| Rule | Description |
|---|---|
| `file` | Value must be a valid uploaded file |
| `image` | Value must be an image (jpeg, png, gif, webp, svg, bmp) |
| `mimes:ext,ext,...` | File must have one of the given extensions |
| `size:N` | File size must not exceed N kilobytes |

### Value Rules

| Rule | Description |
|---|---|
| `array` | Value must be an array |
| `in:a,b,c` | Value must be one of the given values |
| `not_in:a,b,c` | Value must not be any of the given values |
| `regex:/pattern/` | Value must match the regex pattern |

## FluentValidator

The `FluentValidator` provides a chainable API for defining rules inline:

```php
use App\Core\Validation\FluentValidator;

$v = FluentValidator::make($requestData)
    ->field('name')->required()->string()->min(2)->max(255)->end()
    ->field('email')->required()->email()->end()
    ->field('avatar')->file()->image()->size(2048)->end()
    ->field('role')->in(['admin', 'editor', 'user'])->end();

if ($v->fails()) {
    $errors = $v->errors();
}
```

### Available Fluent Methods

```php
$v->field('name')
    ->required($message)
    ->string($message)
    ->email($message)
    ->numeric($message)
    ->integer($message)
    ->min(2, $message)
    ->max(255, $message)
    ->between(1, 10, $message)
    ->array($message)
    ->file($message)
    ->image($message)
    ->mimes('jpg,png,pdf', $message)
    ->size(2048, $message)
    ->in(['a', 'b'], $message)
    ->notIn(['x', 'y'], $message)
    ->regex('/^[a-z]+$/', $message)
    ->same('other_field', $message)
    ->confirmed($message)
    ->unique('users', 'email')
    ->exists('roles', 'id')
    ->end();
```

## FormRequest

For complex validation logic, extend the `FormRequest` class:

```php
<?php

namespace App\Http\Requests;

use App\Core\Validation\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Return false to deny the request
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide your name.',
            'email.unique'  => 'This email is already taken.',
        ];
    }
}
```

Use it in a controller:

```php
public function store(Request $request)
{
    $formRequest = new StoreUserRequest($request->all());

    if ($formRequest->fails()) {
        return redirect()->back()->withErrors($formRequest->errors());
    }

    $validated = $formRequest->validated();
    // $validated contains only the fields defined in rules()
}
```

### FormRequest Methods

| Method | Description |
|---|---|
| `rules()` | Define validation rules (abstract) |
| `messages()` | Custom error messages per field/rule |
| `authorize()` | Determine if the request is authorized |
| `validated()` | Get validated data (throws ValidationException on failure) |
| `passes()` | Check if validation passed |
| `fails()` | Check if validation failed |
| `errors()` | Get validation errors |
| `input(key, default)` | Get a single input value |
| `all()` | Get all input data |

## Custom Error Messages

For the standard `Validator`, pass messages as a global helper:

```php
$validator = Validator::make($data, $rules);
```

Custom messages can be defined in the `messages()` method of a `FormRequest`.

## ValidationException

When calling `validated()` on a `FormRequest` that fails, a `ValidationException` is thrown:

```php
try {
    $validated = $request->validated();
} catch (ValidationException $e) {
    $errors = $e->errors();
}
```
