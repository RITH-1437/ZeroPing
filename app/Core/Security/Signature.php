<?php

namespace App\Core\Security;

use App\Core\Support\Config;

class Signature
{
    public static function sign(string $url): string
    {
        $key = Config::get('security.key');
        $signature = hash_hmac('sha256', $url, $key);

        if (strpos($url, '?') === false) {
            return $url . '?signature=' . $signature;
        }

        return $url . '&signature=' . $signature;
    }

    public static function validate(string $url): bool
    {
        $parts = parse_url($url);
        $signature = '';
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['signature'])) {
                $signature = $query['signature'];
                unset($query['signature']);
                $parts['query'] = http_build_query($query);
            }
        }

        $urlWithoutSignature = $parts['scheme'] . '://' . $parts['host'] . $parts['path'];
        if (isset($parts['query']) && $parts['query']) {
            $urlWithoutSignature .= '?' . $parts['query'];
        }

        $key = Config::get('security.key');
        $expectedSignature = hash_hmac('sha256', $urlWithoutSignature, $key);

        return hash_equals($expectedSignature, $signature);
    }
}
