<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Support\Config;

class Signature
{
    public static function sign(string $url): string
    {
        $key = self::getKey();

        if (str_contains($url, '?')) {
            $separator = '&';
        } else {
            $separator = '?';
        }

        $signature = hash_hmac('sha256', $url, $key);
        return $url . $separator . 'signature=' . $signature;
    }

    public static function validate(string $url): bool
    {
        $key = self::getKey();

        $parts = parse_url($url);
        if ($parts === false || !isset($parts['scheme'], $parts['host'])) {
            return false;
        }

        $signature = '';
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['signature'])) {
                $signature = $query['signature'];
                unset($query['signature']);
                $parts['query'] = http_build_query($query);
            }
        }

        $urlWithoutSignature = strtolower($parts['scheme']) . '://' . strtolower($parts['host']);
        if (isset($parts['port'])) {
            $urlWithoutSignature .= ':' . $parts['port'];
        }
        $urlWithoutSignature .= $parts['path'] ?? '';
        if (!empty($parts['query'])) {
            $urlWithoutSignature .= '?' . $parts['query'];
        }

        $expectedSignature = hash_hmac('sha256', $urlWithoutSignature, $key);
        return hash_equals($expectedSignature, $signature);
    }

    private static function getKey(): string
    {
        $key = Config::get('security.key');
        if ($key === '' || $key === null) {
            throw new \RuntimeException(
                'Application key is not set. Run "php zero key:generate" to generate a secure key.'
            );
        }
        return $key;
    }
}
