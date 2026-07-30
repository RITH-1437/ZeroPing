<?php

declare(strict_types=1);

namespace App\Core\Support;

use App\Core\Logging\FileLogger;
use App\Core\Logging\Logger;
use App\Core\Logging\LogLevel;

/**
 * Static logging facade for convenience access throughout the application.
 *
 * Provides static methods for all PSR-3 log levels. Resolves the Logger
 * instance from the application container if available, otherwise falls
 * back to a direct FileLogger instance.
 *
 * @see Logger
 */
class Log
{
    /**
     * The resolved logger instance.
     */
    protected static ?Logger $logger = null;

    /**
     * Log an emergency-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function emergency(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Log an alert-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function alert(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Log a critical-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function critical(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Log an error-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function error(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Log a warning-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function warning(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Log a notice-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function notice(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Log an info-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function info(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Log a debug-level message.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function debug(string $message, array $context = []): void
    {
        static::getLogger()->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Log a message at an arbitrary level.
     *
     * @param string               $level   The PSR-3 log level.
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public static function log(string $level, string $message, array $context = []): void
    {
        static::getLogger()->log($level, $message, $context);
    }

    /**
     * Get or create the logger instance.
     *
     * @return Logger
     */
    protected static function getLogger(): Logger
    {
        if (static::$logger !== null) {
            return static::$logger;
        }

        if (class_exists(\App\Core\Application\App::class)) {
            try {
                $container = \App\Core\Application\App::container();
                $logger = $container->make(Logger::class);
                if ($logger instanceof Logger) {
                    static::$logger = $logger;
                    return static::$logger;
                }
            } catch (\Throwable) {
                // Fall through to direct instantiation.
            }
        }

        static::$logger = new FileLogger();

        return static::$logger;
    }

    /**
     * Set the logger instance explicitly (useful for testing).
     *
     * @param Logger|null $logger The logger instance or null to reset.
     *
     * @return void
     */
    public static function setLogger(?Logger $logger): void
    {
        static::$logger = $logger;
    }
}
