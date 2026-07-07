<?php

namespace App\Core\Support;

class Log
{
    /**
     * Log an info message.
     *
     * @param  string  $message
     * @return void
     */
    public static function info(string $message): void
    {
        static::log('info', $message);
    }

    /**
     * Log a warning message.
     *
     * @param  string  $message
     * @return void
     */
    public static function warning(string $message): void
    {
        static::log('warning', $message);
    }

    /**
     * Log an error message.
     *
     * @param  string  $message
     * @return void
     */
    public static function error(string $message): void
    {
        static::log('error', $message);
    }

    /**
     * Log a message.
     *
     * @param  string  $level
     * @param  string  $message
     * @return void
     */
    protected static function log(string $level, string $message): void
    {
        $file = BASE_PATH . '/storage/logs/zeroping.log';

        $message = date('[Y-m-d H:i:s] ') . strtoupper($level) . ': ' . $message . PHP_EOL;

        file_put_contents($file, $message, FILE_APPEND);
    }
}
