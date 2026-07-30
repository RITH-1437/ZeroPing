<?php

declare(strict_types=1);

namespace App\Core\Logging;

use InvalidArgumentException;

/**
 * File-based logger implementation following PSR-3 conventions.
 *
 * Writes log entries to a file with timestamp, level, and message.
 * Supports:
 * - PSR-3 context placeholder interpolation ({key} => value).
 * - Minimum log level filtering.
 * - Automatic directory creation for the log file path.
 *
 * @see Logger
 */
class FileLogger implements Logger
{
    /**
     * The absolute path to the log file.
     */
    protected string $path;

    /**
     * The minimum log level to record (severity index from LogLevel::ALL).
     */
    protected int $minimumLevelIndex;

    /**
     * Create a new file logger instance.
     *
     * @param string|null $path         The log file path. Falls back to config or default.
     * @param string      $minimumLevel The minimum PSR-3 level to write (default: debug = all).
     */
    public function __construct(?string $path = null, string $minimumLevel = LogLevel::DEBUG)
    {
        $this->path = $path ?? $this->resolveDefaultPath();
        $this->minimumLevelIndex = $this->levelIndex($minimumLevel);
    }

    /**
     * Log a message at an arbitrary level.
     *
     * @param string               $level   A PSR-3 log level constant.
     * @param string               $message The log message with optional {placeholder} tokens.
     * @param array<string, mixed> $context Context values for interpolation.
     *
     * @return void
     *
     * @throws InvalidArgumentException If the level is not a valid PSR-3 level.
     */
    public function log(string $level, string $message, array $context = []): void
    {
        $level = strtolower($level);

        if (!LogLevel::isValid($level)) {
            throw new InvalidArgumentException("Invalid log level: {$level}");
        }

        if ($this->levelIndex($level) > $this->minimumLevelIndex) {
            return;
        }

        $this->ensureDirectory();

        $interpolated = $this->interpolate($message, $context);
        $timestamp = date('Y-m-d H:i:s');
        $upperLevel = strtoupper($level);

        $entry = "[{$timestamp}] {$upperLevel}: {$interpolated}" . PHP_EOL;

        file_put_contents($this->path, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * System is unusable.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function emergency(string $message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function alert(string $message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
     * Exceptional occurrences that are not errors.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function notice(string $message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string               $message The log message.
     * @param array<string, mixed> $context Contextual data.
     *
     * @return void
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
     * Interpolate context values into the message placeholders.
     *
     * @param string               $message The message with {placeholder} tokens.
     * @param array<string, mixed> $context The context key-value pairs.
     *
     * @return string The interpolated message.
     */
    protected function interpolate(string $message, array $context): string
    {
        if (empty($context)) {
            return $message;
        }

        $replacements = [];

        foreach ($context as $key => $value) {
            if (is_null($value)) {
                $replacements['{' . $key . '}'] = 'null';
            } elseif (is_bool($value)) {
                $replacements['{' . $key . '}'] = $value ? 'true' : 'false';
            } elseif (is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))) {
                $replacements['{' . $key . '}'] = (string) $value;
            } elseif (is_array($value)) {
                $replacements['{' . $key . '}'] = (string) json_encode($value);
            } else {
                $replacements['{' . $key . '}'] = '[object ' . get_class($value) . ']';
            }
        }

        return strtr($message, $replacements);
    }

    /**
     * Ensure the log file directory exists.
     *
     * @return void
     */
    protected function ensureDirectory(): void
    {
        $dir = dirname($this->path);

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
    }

    /**
     * Resolve the default log file path from configuration or framework defaults.
     *
     * @return string The absolute path to the log file.
     */
    protected function resolveDefaultPath(): string
    {
        if (function_exists('config')) {
            $configPath = config('logging.path');
            if (is_string($configPath) && $configPath !== '') {
                return $configPath;
            }
        }

        if (defined('BASE_PATH')) {
            return BASE_PATH . '/storage/logs/app.log';
        }

        return sys_get_temp_dir() . '/zeroping.log';
    }

    /**
     * Get the severity index for a log level (lower index = higher severity).
     *
     * @param string $level The PSR-3 log level.
     *
     * @return int The index in LogLevel::ALL.
     */
    protected function levelIndex(string $level): int
    {
        $index = array_search(strtolower($level), LogLevel::ALL, true);

        return $index !== false ? (int) $index : count(LogLevel::ALL) - 1;
    }
}
