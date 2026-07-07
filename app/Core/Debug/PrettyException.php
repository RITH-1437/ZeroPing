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
        echo '<h1>' . get_class($this->exception) . '</h1>';
        echo '<p>' . $this->exception->getMessage() . '</p>';
        echo '<p>' . $this->exception->getFile() . ':' . $this->exception->getLine() . '</p>';
        echo '<pre>' . $this->exception->getTraceAsString() . '</pre>';
    }
}
