<?php

declare(strict_types=1);

namespace App\Core\Security;

use App\Core\Support\Config;
use RuntimeException;

/**
 * URL signature generator and validator using HMAC-SHA256.
 *
 * Provides tamper-proof URL signing for actions that need to be verified
 * without session state (e.g., email verification links, unsubscribe URLs).
 *
 * Uses hash_equals() for timing-safe signature comparison to prevent
 * timing side-channel attacks.
 */
class Signature
{
    /** @var string HMAC algorithm used for signing. */
    private const ALGORITHM = 'sha256';

    /** @var string Query parameter name for the signature. */
    private const SIGNATURE_PARAM = 'signature';

    /**
     * Sign a URL by appending an HMAC-SHA256 signature as a query parameter.
     *
     * @param string $url The URL to sign.
     * @return string The signed URL with the signature parameter appended.
     *
     * @throws RuntimeException If the application key is not configured.
     */
    public static function sign(string $url): string
    {
        $key = self::getKey();

        $separator = str_contains($url, '?') ? '&' : '?';

        $signature = hash_hmac(self::ALGORITHM, $url, $key);

        return $url . $separator . self::SIGNATURE_PARAM . '=' . $signature;
    }

    /**
     * Validate a signed URL by verifying its HMAC signature.
     *
     * Extracts the signature from the URL, reconstructs the original URL
     * without the signature parameter, and compares the expected HMAC
     * using hash_equals() for timing-safe verification.
     *
     * @param string $url The full signed URL to validate.
     * @return bool True if the signature is valid.
     */
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
            if (isset($query[self::SIGNATURE_PARAM])) {
                $sigValue = $query[self::SIGNATURE_PARAM];
                if (!is_string($sigValue)) {
                    return false;
                }
                $signature = $sigValue;
                unset($query[self::SIGNATURE_PARAM]);
                $parts['query'] = http_build_query($query);
            } else {
                return false;
            }
        } else {
            return false;
        }

        if ($signature === '') {
            return false;
        }

        $urlWithoutSignature = strtolower($parts['scheme']) . '://' . strtolower($parts['host']);

        if (isset($parts['port'])) {
            $urlWithoutSignature .= ':' . $parts['port'];
        }

        $urlWithoutSignature .= $parts['path'] ?? '';

        if (!empty($parts['query'])) {
            $urlWithoutSignature .= '?' . $parts['query'];
        }

        $expectedSignature = hash_hmac(self::ALGORITHM, $urlWithoutSignature, $key);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Retrieve the application signing key from configuration.
     *
     * @return string The signing key.
     *
     * @throws RuntimeException If the key is not configured.
     */
    private static function getKey(): string
    {
        $key = Config::get('security.key');

        if ($key === '' || $key === null) {
            throw new RuntimeException(
                'Application key is not set. Run "php zero key:generate" to generate a secure key.'
            );
        }

        return (string) $key;
    }
}
