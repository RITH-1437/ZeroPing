<?php

namespace App\Core\Console\Commands;

class ServeCommand
{
    protected string $host = '127.0.0.1';

    protected int $port = 1437  ;

    public function handle(array $args = []): void
    {
        $this->parseArguments($args);

        if ($this->isPortInUse()) {
            $this->outputError("Port {$this->port} is already in use.");
            return;
        }

        $this->outputBanner();
        $this->startServer();
    }

    protected function parseArguments(array $args): void
    {
        foreach ($args as $arg) {
            if (str_starts_with($arg, '--host=')) {
                $this->host = substr($arg, 7);
            } elseif (str_starts_with($arg, '--port=')) {
                $port = (int) substr($arg, 7);
                if ($port > 0 && $port <= 65535) {
                    $this->port = $port;
                }
            }
        }
    }

    protected function isPortInUse(): bool
    {
        $connection = @fsockopen($this->host, $this->port, $errno, $errstr, 1);

        if ($connection) {
            fclose($connection);
            return true;
        }

        return false;
    }

    protected function outputBanner(): void
    {
        echo PHP_EOL;
        echo "========================================\n";
        echo " ZeroPing Development Server\n";
        echo "========================================\n\n";
        echo "Server running on:\n\n";
        echo "  http://{$this->host}:{$this->port}\n\n";
        echo "Document Root:\n\n";
        echo "  public/\n\n";
        echo "Press Ctrl+C to stop the server.\n\n";
        echo "========================================\n\n";
    }

    protected function outputError(string $message): void
    {
        echo PHP_EOL;
        echo "ERROR\n\n";
        echo "{$message}\n\n";
    }

    protected function startServer(): void
    {
        $documentRoot = BASE_PATH . DIRECTORY_SEPARATOR . 'public';

        $command = sprintf(
            'php -S %s:%d -t %s',
            $this->host,
            $this->port,
            escapeshellarg($documentRoot)
        );

        passthru($command);
    }
}
