<?php

namespace App\Core\Logging;

class FileLogger implements Logger
{
    protected string $path;

    public function __construct()
    {
        $this->path = config(
            'logging.path',
            BASE_PATH . '/storage/logs/app.log'
        );
    }

    public function log(string $level, string $message): void
    {
        if (!is_dir(dirname($this->path))) {
            mkdir(dirname($this->path), 0777, true);
        }

        $time = date('Y-m-d H:i:s');

        file_put_contents(
            $this->path,
            "[{$time}] {$level}: {$message}" . PHP_EOL,
            FILE_APPEND
        );
    }

    public function emergency(string $message): void
    {
        $this->log(LogLevel::EMERGENCY, $message);
    }

    public function alert(string $message): void
    {
        $this->log(LogLevel::ALERT, $message);
    }

    public function critical(string $message): void
    {
        $this->log(LogLevel::CRITICAL, $message);
    }

    public function error(string $message): void
    {
        $this->log(LogLevel::ERROR, $message);
    }

    public function warning(string $message): void
    {
        $this->log(LogLevel::WARNING, $message);
    }

    public function notice(string $message): void
    {
        $this->log(LogLevel::NOTICE, $message);
    }

    public function info(string $message): void
    {
        $this->log(LogLevel::INFO, $message);
    }

    public function debug(string $message): void
    {
        $this->log(LogLevel::DEBUG, $message);
    }
}
