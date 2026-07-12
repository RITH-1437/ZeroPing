<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class OptimizeCommand extends Command
{
    protected string $signature = 'optimize';
    protected string $description = 'Cache config, routes, views, and search index';

    public function handle(): void
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('search:index');

        // Build an authoritative classmap autoloader so class resolution no
        // longer requires a PSR-4 directory scan on every request.
        $this->optimizeAutoloader();

        $this->info('Application optimized successfully!');
    }

    /**
     * Run `composer dump-autoload -o` to generate a classmap. This is the
     * primary autoloader performance gain (no per-class path probing).
     */
    protected function optimizeAutoloader(): void
    {
        $composer = $this->findComposer();

        if ($composer === null) {
            $this->comment('Skipping autoloader optimization (composer not found).');
            return;
        }

        $cmd = '"' . $composer . '" dump-autoload -o 2>&1';
        $output = shell_exec($cmd);

        if ($output !== null && trim($output) !== '') {
            $this->line(trim($output));
        }

        $this->info('Autoloader classmap generated.');
    }

    /**
     * Locate the composer binary (global or local phar).
     */
    protected function findComposer(): ?string
    {
        $candidates = [
            'composer',
            'composer.phar',
            PHP_BINARY . '/../composer',
            BASE_PATH . '/composer.phar',
        ];

        foreach ($candidates as $candidate) {
            $test = (str_contains($candidate, '/') || str_contains($candidate, '\\'))
                ? $candidate
                : (shell_exec("where $candidate 2>nul") ?: '');
            if (is_executable($candidate) || (is_string($test) && trim($test) !== '')) {
                return $candidate;
            }
        }

        return null;
    }
}
