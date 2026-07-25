<?php

namespace App\Core\Debug;

use Throwable;

class PrettyException
{
    protected Throwable $exception;

    public function __construct(Throwable $exception)
    {
        $this->exception = $exception;
    }

    public function render(): void
    {
        echo '<h1>' . htmlspecialchars(get_class($this->exception), ENT_QUOTES, 'UTF-8') . '</h1>';
        echo '<p>' . htmlspecialchars($this->exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p>'
            . htmlspecialchars($this->exception->getFile(), ENT_QUOTES, 'UTF-8') . ':'
            . htmlspecialchars($this->exception->getLine(), ENT_QUOTES, 'UTF-8')
            . '</p>';
        echo '<pre>' . htmlspecialchars($this->exception->getTraceAsString(), ENT_QUOTES, 'UTF-8') . '</pre>';
    }
}
