<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Publish framework assets into the application.
 *
 * Copies the canonical, framework-provided versions of config files,
 * error views, language files and public assets into the project when
 * they are missing — the equivalent of `vendor:publish` for a
 * from-scratch framework. Existing files are never overwritten.
 */
class PublishCommand extends Command
{
    protected string $signature = 'publish';

    protected string $description = 'Publish framework config, views, lang and public assets';

    /**
     * @var array<string, array{source: string, dest: string, label: string}>
     */
    private const GROUPS = [
        'config' => ['source' => 'stubs/publish/config', 'dest' => 'config', 'label' => 'Configuration'],
        'views'  => ['source' => 'stubs/publish/views',  'dest' => 'views',  'label' => 'Views'],
        'lang'   => ['source' => 'stubs/publish/lang',   'dest' => 'lang',   'label' => 'Language files'],
        'public' => ['source' => 'stubs/publish/public', 'dest' => 'public', 'label' => 'Public assets'],
    ];

    public function handle(): void
    {
        $requested = $this->option('group') ?? 'all';

        if ($requested !== 'all' && !isset(self::GROUPS[$requested])) {
            $this->error("Unknown publish group '{$requested}'.");
            $this->line('  <fg=gray>Available groups: </> <fg=cyan>' . implode(', ', array_keys(self::GROUPS)) . ', all</>');
            return;
        }

        $groups = $requested === 'all'
            ? array_keys(self::GROUPS)
            : [$requested];

        $this->title('Publish Framework Assets');

        $totalCopied = 0;

        foreach ($groups as $group) {
            $copied = $this->publishGroup($group);
            $totalCopied += $copied;
        }

        $this->line('');

        if ($totalCopied === 0) {
            $this->info('Nothing to publish — all framework assets are already present.');
        } else {
            $this->success("Published {$totalCopied} file(s).");
        }
    }

    private function publishGroup(string $group): int
    {
        $def   = self::GROUPS[$group];
        $source = $this->basePath() . DIRECTORY_SEPARATOR . $def['source'];
        $dest   = $this->basePath() . DIRECTORY_SEPARATOR . $def['dest'];

        $this->section($def['label'] . ' → ' . $def['dest']);

        if (!is_dir($source)) {
            $this->warn("No publishable source found for '{$group}'.");
            return 0;
        }

        if (!is_dir($dest)) {
            @mkdir($dest, 0775, true);
        }

        $copied = 0;

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relative = $iterator->getSubPathname();
            $target   = $dest . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    @mkdir($target, 0775, true);
                }
                continue;
            }

            if (file_exists($target)) {
                continue;
            }

            if (!is_dir(dirname($target))) {
                @mkdir(dirname($target), 0775, true);
            }

            if (@copy((string) $item->getRealPath(), $target)) {
                $copied++;
                $this->line('  <fg=green>✔</> <fg=white>' . $def['dest'] . '/' . $relative . '</>');
            } else {
                $this->warn('Could not copy ' . $relative);
            }
        }

        if ($copied === 0) {
            $this->line('  <fg=gray>Already up to date.</>');
        }

        return $copied;
    }

    private function basePath(): string
    {
        return defined('BASE_PATH') ? rtrim((string) BASE_PATH, '/\\') : (string) getcwd();
    }
}
