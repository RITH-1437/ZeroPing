<?php

/**
 * Default validation language file (English).
 *
 * Publishes into lang/en/validation.php via `php zero publish --group=lang`.
 * Read it with `lang('validation.required')` after registering a Lang
 * service provider, or use it as a reference when localizing your app.
 */
return [

    'required'    => 'The :attribute field is required.',
    'email'       => 'The :attribute must be a valid email address.',
    'min'         => 'The :attribute must be at least :min characters.',
    'max'         => 'The :attribute must not exceed :max characters.',
    'numeric'     => 'The :attribute must be a number.',
    'unique'      => 'The :attribute has already been taken.',
    'confirmed'   => 'The :attribute confirmation does not match.',
    'in'          => 'The :attribute must be one of: :values.',
    'string'      => 'The :attribute must be a string.',
    'array'       => 'The :attribute must be an array.',

    'attributes' => [
        'name'     => 'name',
        'email'    => 'email',
        'password' => 'password',
    ],

];
