<?php

namespace App\Core\Console;

class ConsoleStyle
{
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
     * Write a line to the console with optional styling.
     *
     * Supported inline tags:
     *   <fg=color>text</>  — Set foreground color (black, red, green, yellow, blue, magenta, cyan, white, gray)
     *   <bg=color>text</>  — Set background color (same palette)
     *
     * @param string $text The text to output, with optional <fg=color> / <bg=color> tags.
     */
    public function writeln(string $text): void
    {
        echo $this->format($text) . PHP_EOL;
    }

    public function write(string $text): void
    {
        echo $this->format($text);
    }

    /**
     * Convert inline style tags to ANSI escape codes.
     *
     * Supports arbitrarily nested tags. When a tag closes, the parent's
     * style is restored so surrounding text keeps its colour, e.g.
     *   <fg=white>Name <fg=gray>[default]</> here</>
     *
     * @param string $text
     */
    private function format(string $text): string
    {
        $token = '/<(\/|options=[a-z]+;fg=[a-z]+;bg=[a-z]+|options=[a-z]+;fg=[a-z]+|fg=[a-z]+;bg=[a-z]+|fg=[a-z]+|bg=[a-z]+)>/';

        $parts = preg_split($token, $text, -1, PREG_SPLIT_DELIM_CAPTURE);

        if ($parts === false) {
            return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

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
     * Build an ANSI escape sequence from a tag descriptor such as
     * "options=bold;fg=white;bg=green".
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
