<?php

declare(strict_types=1);

namespace App\Core\Console;

/**
 * Console output styling with inline ANSI color tags.
 *
 * Supports inline tags for foreground, background colors and text options:
 *   - `<fg=color>text</>` — set foreground color
 *   - `<bg=color>text</>` — set background color
 *   - `<options=bold;fg=color>text</>` — bold + color
 *   - `<fg=color;bg=color>text</>` — foreground + background
 *
 * Available colors: black, red, green, yellow, blue, magenta, cyan, white, gray.
 * Tags may be nested; closing `</>` restores the parent style.
 *
 * @package App\Core\Console
 */
class ConsoleStyle
{
    /**
     * Foreground color name => ANSI code.
     *
     * @var array<string, string>
     */
    private const COLORS = [
        'black'   => '30',
        'red'     => '31',
        'green'   => '32',
        'yellow'  => '33',
        'blue'    => '34',
        'magenta' => '35',
        'cyan'    => '36',
        'white'   => '37',
        'gray'    => '90',
    ];

    /**
     * Background color name => ANSI code.
     *
     * @var array<string, string>
     */
    private const BG_COLORS = [
        'black'   => '40',
        'red'     => '41',
        'green'   => '42',
        'yellow'  => '43',
        'blue'    => '44',
        'magenta' => '45',
        'cyan'    => '46',
        'white'   => '47',
        'gray'    => '100',
    ];

    /**
     * Write a styled line to stdout, followed by a newline.
     */
    public function writeln(string $text): void
    {
        echo $this->format($text) . PHP_EOL;
    }

    /**
     * Write styled text to stdout without a trailing newline.
     */
    public function write(string $text): void
    {
        echo $this->format($text);
    }

    /**
     * Convert inline style tags to ANSI escape codes.
     *
     * Supports arbitrarily nested tags. When a tag closes, the parent's
     * style is restored so surrounding text keeps its colour, e.g.
     *   `<fg=white>Name <fg=gray>[default]</> here</>`
     */
    private function format(string $text): string
    {
        $token = '/<(\/|options=[a-z]+;fg=[a-z]+;bg=[a-z]+|options=[a-z]+;fg=[a-z]+|fg=[a-z]+;bg=[a-z]+|fg=[a-z]+|bg=[a-z]+)>/';

        $parts = preg_split($token, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        /** @var string[] $stack ANSI sequences for nested style restoration */
        $stack = [];
        $out = '';

        foreach ($parts as $index => $part) {
            if ($index % 2 === 0) {
                $out .= $part;
                continue;
            }

            if ($part === '/') {
                if ($stack !== []) {
                    array_pop($stack);
                }
                $out .= "\033[0m";
                if ($stack !== []) {
                    $out .= implode('', $stack);
                }
                continue;
            }

            $ansi = $this->ansiFor($part);
            $stack[] = $ansi;
            $out .= $ansi;
        }

        if ($stack !== []) {
            $out .= "\033[0m";
        }

        // Decode HTML entities like &lt; &gt; after parsing so they cannot be
        // mistaken for style tags.
        return html_entity_decode($out, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * Build an ANSI escape sequence from a tag descriptor.
     *
     * Examples: "fg=green", "options=bold;fg=white", "fg=cyan;bg=black"
     */
    private function ansiFor(string $descriptor): string
    {
        $codes = [];

        foreach (explode(';', $descriptor) as $segment) {
            if (!str_contains($segment, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $segment, 2);

            if ($key === 'options') {
                $codes[] = $value === 'bold' ? '1' : '0';
            } elseif ($key === 'fg') {
                $codes[] = self::COLORS[$value] ?? '37';
            } elseif ($key === 'bg') {
                $codes[] = self::BG_COLORS[$value] ?? '40';
            }
        }

        return "\033[" . implode(';', $codes) . 'm';
    }
}
