<?php

namespace App\Core\Debug;

use Throwable;

class ExceptionHandler
{
    public function handle(Throwable $e): void
    {
        if (config('app.debug')) {
            $this->renderPrettyException($e);
        } else {
            $this->renderProductionException($e);
        }
    }

    protected function renderPrettyException(Throwable $e): void
    {
        $pretty = new PrettyException($e);
        $pretty->render();
    }

    protected function renderProductionException(Throwable $e): void
    {
        // A real implementation would show a generic error page.
        echo '<h1>500 Internal Server Error</h1>';
    }
}
