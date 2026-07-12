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
        $frameworkDir = getcwd();
        $sourceDir = dirname(__DIR__, 1) . '/Templates/' . $type;

        if (!is_dir($sourceDir)) {
            (new \App\Core\Console\ConsoleStyle())->writeln("<fg=red>Error:</> Template '<fg=white>$type</>' not found.");
            return;
        }

        if (is_dir($targetDir)) {
            (new \App\Core\Console\ConsoleStyle())->writeln("<fg=red>Error:</> Target directory already exists: <fg=white>$targetDir</>");
            return;
        }

        $style = new \App\Core\Console\ConsoleStyle();
        $style->writeln("<fg=green>Creating <fg=white>$type</> project in <fg=white>$targetDir</>...</>");

        // 1. Copy the framework (self-contained) into the target.
        $this->copyFramework($frameworkDir, $targetDir);

        // 2. Overlay the chosen template's app/config/views/etc.
        $this->copyDirectory($sourceDir, $targetDir, $projectName, $type);

        // 3. Brand the generated composer.json for the new project.
        $this->brandComposer($targetDir, $projectName);

        // 4. Prepare environment so the app boots after `composer install`.
        $this->prepareEnv($targetDir);

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
            $relative = $items->getSubPathname();

            if ($this->isExcluded($relative)) {
                continue;
            }

            $dest = $target . '/' . $relative;

            if ($item->isDir()) {
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
            } else {
                if (!is_dir(dirname($dest))) {
                    mkdir(dirname($dest), 0755, true);
                }
                copy($item->getRealPath(), $dest);
            }
        }
    }

    /**
     * Paths from the framework source that should NOT be copied into a new app:
     * VCS/tooling dirs, the demo website, and the bundled starter templates.
     */
    private function isExcluded(string $relative): bool
    {
        $top = explode('/', $relative)[0];

        $excludeTop = ['.git', 'vendor', 'node_modules', 'arena', 'docs', 'tests', '.github', 'composer.lock', '.env', 'storage'];
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

    private function prepareEnv(string $dir): void
    {
        $envPath = $dir . '/.env';
        $examplePath = $dir . '/.env.example';

        if (!file_exists($envPath) && file_exists($examplePath)) {
            copy($examplePath, $envPath);
        }

        if (file_exists($envPath)) {
            $env = (string) file_get_contents($envPath);

            if (!preg_match('/^APP_KEY=/m', $env)) {
                $env .= "\nAPP_KEY=\n";
            }

            if (preg_match('/^APP_KEY=(.*)$/m', $env, $m) && $m[1] === '') {
                $key = 'base64:' . base64_encode(random_bytes(32));
                $env = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY=' . $key, $env);
                file_put_contents($envPath, $env);
            }
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
            $relative = $items->getSubPathname();
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
