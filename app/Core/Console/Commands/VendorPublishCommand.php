<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

/**
 * Publish assets declared by installed packages via ServiceProvider::publishes().
 *
 * Each booted provider records its publishable paths (grouped) on the package
 * base ServiceProvider. This command copies those files into the host app,
 * skipping anything already present unless --force is given.
 */
class VendorPublishCommand extends Command
{
    protected string $description = 'Publish package assets (config, views, migrations, ...)';

    public function handle(): void
    {
        if (!class_exists(\Zeroping\Support\ServiceProvider::class)) {
            $this->error('No publishable packages found (ZeroPing support package missing).');

            return;
        }

        $all = \Zeroping\Support\ServiceProvider::allPublished();

        if ($all === []) {
            $this->info('Nothing to publish — no packages have declared publishable assets.');

            return;
        }

        $group = $this->option('group') ?? 'all';

        if ($group !== 'all' && !isset($all[$group])) {
            $this->error("Unknown publish group '{$group}'.");
            $this->line('  <fg=gray>Available groups: </> <fg=cyan>' . implode(', ', array_keys($all)) . ', all</>');

            return;
        }

        $groups = $group === 'all' ? array_keys($all) : [$group];

        $this->title('Publish Package Assets');

        $copied = 0;

        foreach ($groups as $g) {
            $this->section('Group: ' . $g);

            foreach ($all[$g] as $from => $to) {
                $copied += $this->publishFile($from, $to);
            }
        }

        $this->line('');

        if ($copied === 0) {
            $this->info('Nothing to publish — all assets already present.');
        } else {
            $this->success("Published {$copied} file(s).");
        }
    }

    private function publishFile(string $from, string $to): int
    {
        if (!file_exists($from)) {
            $this->warn('Source missing: ' . $from);

            return 0;
        }

        if (file_exists($to) && !$this->option('force')) {
            $this->line('  <fg=gray>exists</> <fg=white>' . $to . '</>');

            return 0;
        }

        $dir = dirname($to);

        if (!is_dir($dir)) {
            @mkdir($dir, 0777, true);
        }

        if (@copy($from, $to)) {
            $this->line('  <fg=green>✔</> <fg=white>' . $to . '</>');

            return 1;
        }

        $this->warn('Could not copy ' . $from);

        return 0;
    }
}
