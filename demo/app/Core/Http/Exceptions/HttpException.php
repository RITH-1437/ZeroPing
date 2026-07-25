<?php

namespace App\Core\Http\Exceptions;

use RuntimeException;

/**
 * An exception that maps to a specific HTTP status code.
 *
 * Throwing one of these from a controller or middleware renders the matching
 * error page (views/errors/{code}.php) and sets the correct response
 * code, keeping error handling declarative instead of raw die()s.
 */
class HttpException extends RuntimeException
{
    public function __construct(
        int $code,
        string $message = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
