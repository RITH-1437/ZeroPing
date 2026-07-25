<?php

namespace App\Core\Exceptions;

class NotFoundException extends \Exception
{
    protected string $defaultMessage = 'Record not found.';

    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message ?: $this->defaultMessage, $code, $previous);
    }
}
