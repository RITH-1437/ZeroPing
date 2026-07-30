<?php

declare(strict_types=1);

namespace App\Core\Logging;

/**
 * PSR-3 inspired logger interface.
 *
 * Defines the eight standard log levels from RFC 5424 and PSR-3.
 * All methods accept a message and an optional context array for
 * placeholder interpolation.
 *
 * @see https://www.php-fig.org/psr/psr-3/
 */
interface Logger
{
    /**
     * System is unusable.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function emergency(string $message, array $context = []): void;

    /**
     * Action must be taken immediately.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function alert(string $message, array $context = []): void;

    /**
     * Critical conditions.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function critical(string $message, array $context = []): void;

    /**
     * Runtime errors that do not require immediate action.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function error(string $message, array $context = []): void;

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function warning(string $message, array $context = []): void;

    /**
     * Normal but significant events.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function notice(string $message, array $context = []): void;

    /**
     * Interesting events.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function info(string $message, array $context = []): void;

    /**
     * Detailed debug information.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function debug(string $message, array $context = []): void;

    /**
     * Log a message at an arbitrary level.
     *
     * @param string               $level   The PSR-3 log level.
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data for placeholders.
     *
     * @return void
     */
    public function log(string $level, string $message, array $context = []): void;
}
