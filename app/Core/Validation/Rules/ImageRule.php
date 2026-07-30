<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

/**
 * Validates that a field is an uploaded image file.
 *
 * Extends FileRule to first verify it is a valid upload, then checks
 * that the file's MIME type corresponds to a supported image format.
 * Supported formats: JPEG, PNG, GIF, WebP, SVG, BMP.
 */
class ImageRule extends FileRule
{
    /**
     * Supported image MIME types.
     *
     * @var string[]
     */
    private const IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/bmp',
    ];

    /**
     * {@inheritDoc}
     */
    public function validate(
        string $field,
        mixed $value,
        array $data = [],
        array $parameters = []
    ): bool {
        if (!parent::validate($field, $value, $data, $parameters)) {
            return false;
        }

        if ($this->isEmpty($value)) {
            return true;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        if ($finfo === false) {
            return false;
        }

        $mime = finfo_file($finfo, $value['tmp_name']);
        finfo_close($finfo);

        if ($mime === false) {
            return false;
        }

        return in_array($mime, self::IMAGE_MIMES, true);
    }

    /**
     * {@inheritDoc}
     */
    public function message(string $field, array $parameters = []): string
    {
        return "The {$this->formatFieldName($field)} field must be an image (jpeg, png, gif, webp, svg, or bmp).";
    }
}
