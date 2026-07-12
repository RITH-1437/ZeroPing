<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\View\View;

class ViewCacheCommand extends Command
{
    protected string $signature = 'view:cache';
    protected string $description = 'Compile all views into cached files';

    public function handle(): void
    {
        $viewsDir = BASE_PATH . '/views';
        $cacheDir = View::cachePath();

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($viewsDir, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $skipDirs = ['components', 'emails', 'layouts'];

        foreach ($iterator as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = str_replace([$viewsDir . '/', $viewsDir . '\\'], '', $file->getPathname());
            $viewName = str_replace('.php', '', $relative);
            $viewName = str_replace('\\', '/', $viewName);

            $skip = false;
            foreach ($skipDirs as $dir) {
                if (str_starts_with($viewName, $dir . '/') || $viewName === $dir) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }

            $content = View::render($viewName, [], $this->detectLayout($viewName));

            $cacheKey = md5($viewName);
            file_put_contents($cacheDir . '/' . $cacheKey . '.php', $content);
            $count++;
        }

        $this->info("Views cached successfully! ({$count} files)");
    }

    private function detectLayout(string $view): string
    {
        if (str_starts_with($view, 'layouts/')) {
            return 'guest';
        }

        if (str_starts_with($view, 'errors/')) {
            return 'guest';
        }

        return 'site';
    }
}
