<?php

declare(strict_types=1);

namespace App\Core\Validation\Rules;

class MimesRule extends FileRule
{
    private const EXTENSION_MIME_MAP = [
        'jpg' => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'bmp' => ['image/bmp'],
        'svg' => ['image/svg+xml'],
        'webp' => ['image/webp'],
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'csv' => ['text/csv', 'text/plain'],
        'txt' => ['text/plain'],
        'zip' => ['application/zip'],
        'rar' => ['application/vnd.rar'],
        'mp3' => ['audio/mpeg'],
        'mp4' => ['video/mp4'],
        'json' => ['application/json'],
        'xml' => ['application/xml', 'text/xml'],
    ];

    public function validate(string $field, mixed $value, array $data = [], array $parameters = []): bool
    {
        if (!parent::validate($field, $value, $data, $parameters)) {
            return false;
        }

        if ($this->isEmpty($value)) {
            return true;
        }

        if (empty($parameters)) {
            return true;
        }

        $extension = strtolower(pathinfo($value['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $parameters, true)) {
            return false;
        }

        if (isset($value['tmp_name'])) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($value['tmp_name']);

            $expectedMimes = self::EXTENSION_MIME_MAP[$extension] ?? [];
            if (!empty($expectedMimes) && !in_array($mimeType, $expectedMimes, true)) {
                return false;
            }
        }

        return true;
    }

    public function message(string $field, array $parameters = []): string
    {
        $allowed = implode(', ', $parameters);
        return "{$field} must be a file of type: {$allowed}.";
    }
}
