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

    public function writeln(string $text): void
    {
        echo $this->format($text) . PHP_EOL;
    }

    public function write(string $text): void
    {
        echo $this->format($text);
    }

    private function format(string $text): string
    {
        // Replace <fg=color>...</>
        $text = preg_replace_callback(
            '/<fg=([a-z]+)>(.*?)<\/>/s',
            function (array $m): string {
                $code = self::COLORS[$m[1]] ?? '37';
                return "\033[{$code}m{$m[2]}\033[0m";
            },
            $text
        );

        // Decode HTML entities like &lt; &gt;
        return html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
