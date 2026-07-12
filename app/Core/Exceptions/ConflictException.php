<?php

namespace App\Core\Exceptions;

class ConflictException extends \Exception
{
    protected string $defaultMessage = 'Conflict occurred while processing the request.';

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: $this->defaultMessage, $code, $previous);
    }
}
