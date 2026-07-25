<?php

namespace App\Core\Security;

class URLSigner
{
    public static function signedRoute(string $name, array $parameters = []): string
    {
        $url = route($name, $parameters);
        return Signature::sign($url);
    }

    public static function temporarySignedRoute(string $name, int $expiration, array $parameters = []): string
    {
        $url = route($name, $parameters, true, $expiration);
        return Signature::sign($url);
    }
}
