<?php

namespace App\Core\Console\Commands;

class NewCommand
{
    private array $templates = [
        'empty' => 'Minimal project skeleton with a single welcome page',
        'blog'  => 'Blog with posts, categories, and pagination',
        'api'   => 'RESTful API with authentication boilerplate',
        'mvc'   => 'Full MVC CRUD scaffold with user management',
        'dashboard' => 'Admin dashboard with stats and user management',
    ];

    private array $descriptions = [
        'empty' => 'A minimal ZeroPing project skeleton with a welcome page',
        'blog'  => 'A blog application built with ZeroPing, featuring posts and categories',
        'api'   => 'A RESTful API boilerplate with authentication endpoints built with ZeroPing',
        'mvc'   => 'A full MVC CRUD application with user management built with ZeroPing',
        'dashboard' => 'An admin dashboard with summary widgets and user management built with ZeroPing',
    ];

    public function handle(string $type, array $options): void
    {
        $type = strtolower($type);

        if (!isset($this->templates[$type])) {
            echo "Usage: php zero new {type} [--name=...] [--dir=...]\n\n";
            echo "Available project types:\n";
            foreach ($this->templates as $key => $desc) {
                echo sprintf("  %-8s %s\n", $key, $desc);
            }
            echo "\n";
            return;
        }

        $projectName = $this->getOption($options, 'name') ?? 'My App';
        $targetDir = $this->getOption($options, 'dir') ?? getcwd() . '/' . $this->slugify($projectName);

        $this->scaffold($type, $targetDir, $projectName);
    }

    private function scaffold(string $type, string $targetDir, string $projectName): void
    {
        $style = new \App\Core\Console\ConsoleStyle();
        $frameworkDir = getcwd();
        $sourceDir = dirname(__DIR__, 1) . '/Templates/' . $type;

        if (!is_dir($sourceDir)) {
            $style->writeln("<fg=red>✗ Unknown template '</><fg=white>{$type}</><fg=red>'.</>");
            $style->writeln("  <fg=gray>Available templates:</> " . implode(', ', array_keys($this->templates)));
            $style->writeln("  <fg=gray>Example:</> <fg=cyan>php zero new empty --name=\"My App\"</>");
            return;
        }

        if (is_dir($targetDir)) {
            $style->writeln("<fg=red>✗ Target directory already exists:</> <fg=white>{$targetDir}</>");
            $style->writeln("  <fg=gray>Choose another name, or remove the existing directory first.</>");
            return;
        }

        $parent = dirname($targetDir);
        if (!is_dir($parent)) {
            @mkdir($parent, 0755, true);
        }
        if (!is_dir($parent) || !is_writable($parent)) {
            $style->writeln("<fg=red>✗ Cannot write to:</> <fg=white>{$parent}</>");
            $style->writeln("  <fg=gray>Check the folder permissions and try again.</>");
            return;
        }

        $style->writeln("<fg=green>Creating </><fg=white>{$type}</><fg=green> project in </><fg=white>{$targetDir}</><fg=green>...</>");

        try {
            // 1. Copy the framework (self-contained) into the target.
            $this->copyFramework($frameworkDir, $targetDir);

            // 2. Overlay the chosen template's app/config/views/etc.
            $this->copyDirectory($sourceDir, $targetDir, $projectName, $type);

            // 3. Brand the generated composer.json for the new project.
            $this->brandComposer($targetDir, $projectName);

            // 4. Prepare environment so the app boots after `composer install`.
            $this->prepareEnv($targetDir, $projectName);
        } catch (\Throwable $e) {
            $style->writeln("<fg=red>✗ Project creation failed:</> <fg=white>{$e->getMessage()}</>");
            $style->writeln("  <fg=gray>Partially created files may remain in:</> {$targetDir}");
            return;
        }

        $style->writeln("");
        $style->writeln("<fg=green>✔ Done!</> Project created at <fg=cyan>$targetDir</>");
        $style->writeln("");
        $style->writeln("<fg=yellow>▸ Quick start:</>");
        $style->writeln("");
        $style->writeln("  <fg=green>\$</> <fg=white>cd</> <fg=cyan>$targetDir</>");
        $style->writeln("  <fg=green>\$</> <fg=white>composer install</>");
        $style->writeln("  <fg=green>\$</> <fg=white>php zero serve</>");
        $style->writeln("");
        $style->writeln("  <fg=gray>Then open http://localhost:1437 in your browser</>");
        $style->writeln("");
    }

    /**
     * Copy the framework into the target as a self-contained project.
     * The framework lives inside the project (under app/Core), so the new app
     * is fully usable after `composer install` — no path-repo dependency.
     */
    private function copyFramework(string $source, string $target): void
    {
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            // Normalize separators so exclusion works on Windows too.
            $relative = str_replace('\\', '/', $items->getSubPathname());

            if ($this->isExcluded($relative)) {
                continue;
            }

            $dest = $target . '/' . $relative;

            // Skip reparse points / junctions (common in node_modules on Windows).
            $sourcePath = $item->getPathname();
            if (is_dir($sourcePath) && !$item->isDir()) {
                continue;
            }

            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
            } else {
                if (!is_dir(dirname($dest))) {
                    mkdir(dirname($dest), 0755, true);
                }
                if (!@copy($sourcePath, $dest)) {
                    // Non-fatal: report and continue so one bad file doesn't
                    // abort the whole scaffold.
                    (new \App\Core\Console\ConsoleStyle())
                        ->writeln("<fg=yellow>  skipped</> <fg=gray>{$relative}</>");
                }
            }
        }
    }

    /**
     * Paths from the framework source that should NOT be copied into a new app:
     * VCS/tooling dirs, the demo website, and the bundled starter templates.
     */
    private function isExcluded(string $relative): bool
    {
        $relative = str_replace('\\', '/', $relative);
        $top = explode('/', $relative)[0];

        $excludeTop = [
            '.git', '.github', '.idea', '.devcontainer', '.docker', '.mimocode',
            '.phpunit.cache', 'vendor', 'node_modules', 'arena', 'docs', 'tests',
            'bench', 'packages', 'composer.lock', '.env', 'storage',
            'package.json', 'package-lock.json',
        ];
        if (in_array($top, $excludeTop, true)) {
            return true;
        }

        $excluded = [
            'app/Core/Console/Templates',
            'views/site',
            'views/home',
            'views/components',
            'views/layouts/site.php',
        ];

        foreach ($excluded as $path) {
            if ($relative === $path || str_starts_with($relative, $path . '/')) {
                return true;
            }
        }

        return false;
    }

    private function brandComposer(string $dir, string $projectName): void
    {
        $file = $dir . '/composer.json';
        if (!file_exists($file)) {
            return;
        }

        $json = json_decode(file_get_contents($file), true);
        if ($json === null) {
            return;
        }

        $slug = $this->slugify($projectName);
        $json['name'] = 'zeroping/' . $slug;
        $json['description'] = $projectName . ' — built with ZeroPing';

        unset($json['repositories']);

        file_put_contents($file, json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
    }

    private function prepareEnv(string $dir, string $projectName = 'ZeroPing'): void
    {
        $envPath = $dir . '/.env';
        $examplePath = $dir . '/.env.example';

        if (!file_exists($envPath) && file_exists($examplePath)) {
            copy($examplePath, $envPath);
        }

        if (file_exists($envPath)) {
            $env = (string) file_get_contents($envPath);

            // Brand the environment with the project name.
            $appName = preg_match('/\s/', $projectName) ? '"' . $projectName . '"' : $projectName;
            if (preg_match('/^APP_NAME=/m', $env)) {
                $env = preg_replace('/^APP_NAME=.*$/m', 'APP_NAME=' . $appName, $env);
            } else {
                $env = "APP_NAME={$appName}\n" . $env;
            }

            if (!preg_match('/^APP_KEY=/m', $env)) {
                $env .= "\nAPP_KEY=\n";
            }

            if (preg_match('/^APP_KEY=(.*)$/m', $env, $m) && $m[1] === '') {
                $key = 'base64:' . base64_encode(random_bytes(32));
                $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
            }

            file_put_contents($envPath, $env);
        }
    }

    private function copyDirectory(string $source, string $dest, string $projectName, string $type): void
    {
        if (!is_dir($source)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $relative = str_replace('\\', '/', $items->getSubPathname());
            $target = $dest . '/' . $relative;

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                $content = file_get_contents($item->getRealPath());
                if ($content === false) {
                    continue;
                }
                if (!is_dir(dirname($target))) {
                    mkdir(dirname($target), 0755, true);
                }
                $content = str_replace(
                    ['{{ project_name }}', '{{ project_type }}', '{{ project_slug }}', '{{ vendor }}', '{{ project_description }}', '{{ php_version }}'],
                    [$projectName, strtoupper($type), $this->slugify($projectName), 'zeroping', $this->descriptions[$type], PHP_VERSION],
                    $content
                );
                file_put_contents($target, $content);
            }
        }
    }

    private function getOption(array $options, string $key): ?string
    {
        foreach ($options as $opt) {
            if (str_starts_with($opt, "--$key=")) {
                return substr($opt, strlen("--$key="));
            }
        }
        return null;
    }

    private function slugify(string $name): string
    {
        return strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $name), '-'));
    }
}
