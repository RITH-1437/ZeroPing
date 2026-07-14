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
        http_response_code(500);

        $view = BASE_PATH . '/views/errors/500.php';

        if (file_exists($view)) {
            $message    = $e->getMessage();
            $exception  = get_class($e);
            $file       = $e->getFile();
            $line       = $e->getLine();
            $trace      = $e->getTrace();

            $requestUrl     = $_SERVER['REQUEST_URI'] ?? '/';
            $requestMethod  = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $environment    = $_ENV['APP_ENV'] ?? 'production';
            $debug          = false;
            $active         = '';
            $title          = '500 - Server Error';

            require $view;
            return;
        }

        echo '<h1>500 Internal Server Error</h1>';
    }
}
