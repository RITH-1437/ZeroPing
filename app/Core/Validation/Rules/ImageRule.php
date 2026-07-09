<?php

namespace App\Core\Validation\Rules;

class ImageRule extends FileRule
{
    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
    ];

    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if (!parent::validate($field, $value, $data, $parameters)) {
            return false;
        }

        if ($this->isEmpty($value)) {
            return true;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);

        return in_array($mime, self::IMAGE_MIMES, true);
    }

    public function message(string $field, array $parameters = []): string
    {
        return "{$field} must be an image (jpeg, png, gif, webp, svg, or bmp).";
    }
}
