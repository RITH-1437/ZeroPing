<?php

declare(strict_types=1);

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Packages\PackageConfig;
use App\Core\Packages\ProviderRepository;
use App\Core\Packages\StarterKit;

class PackageCreateCommand extends Command
{
    protected string $description = 'Scaffold a new ZeroPing package';

    public function handle(string $name): void
    {
        if (empty($name)) {
            $this->warn('Usage: php zero package:create <Name>');

            return;
        }

        $studly = ucfirst(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $kebab = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $studly));

        $vendor   = 'zeroping';
        $pkgName  = $vendor . '/' . $kebab;
        $ns           = 'Zeroping\\' . $studly;
        $nsPrefix     = $ns . '\\';
        $nsPrefixJson = str_replace('\\', '\\\\', $nsPrefix);
        $dir          = BASE_PATH . '/packages/' . $vendor . '/' . $kebab;

        if (is_dir($dir)) {
            $this->error("Package already exists: {$dir}");

            return;
        }

        foreach (['src', 'routes', 'config', 'database/migrations', 'views', 'assets', 'tests'] as $sub) {
            mkdir($dir . '/' . $sub, 0777, true);
        }

        $composer = <<<JSON
{
    "name": "{$pkgName}",
    "type": "library",
    "description": "{$studly} package for the ZeroPing Framework",
    "license": "MIT",
    "require": {
        "php": ">=8.1"
    },
    "autoload": {
        "psr-4": {
            "{$nsPrefixJson}": "src/"
        }
    },
    "extra": {
        "zeroping": {
            "providers": [
                "{$nsPrefixJson}{$studly}ServiceProvider"
            ]
        }
    }
}
JSON;
        file_put_contents($dir . '/composer.json', $composer);

        $provider = <<<PHP
<?php

namespace {$ns};

use Zeroping\Support\ServiceProvider;

class {$studly}ServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind services into the container.
    }

    public function boot(): void
    {
        // \$this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        // \$this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        // \$this->loadViewsFrom(__DIR__ . '/../views', '{$kebab}');
        // \$this->commands([{$studly}ControllerCommand::class]);
        // \$this->publishes([__DIR__ . '/../config/{$kebab}.php' => base_path('config/{$kebab}.php')], '{$kebab}-config');
    }
}
PHP;
        file_put_contents($dir . '/src/' . $studly . 'ServiceProvider.php', $provider);

        file_put_contents($dir . '/config/' . $kebab . '.php', "<?php\n\nreturn [\n    // package config\n];\n");
        file_put_contents($dir . '/routes/web.php', "<?php\n\n// Register routes with Router::get/post(...).\n");
        file_put_contents($dir . '/views/.gitkeep', '');
        file_put_contents($dir . '/assets/.gitkeep', '');
        file_put_contents($dir . '/database/migrations/.gitkeep', '');

        $test = <<<PHP
<?php

namespace {$namespace}Tests;

use PHPUnit\Framework\TestCase;

class {$studly}Test extends TestCase
{
    public function testWorks(): void
    {
        \$this->assertTrue(true);
    }
}
PHP;
        file_put_contents($dir . '/tests/' . $studly . 'Test.php', $test);

        file_put_contents($dir . '/README.md', "# {$pkgName}\n\nZeroPing package.\n");

        $repo = new ProviderRepository(BASE_PATH, BASE_PATH . '/bootstrap/cache/packages.php');
        $repo->cache($repo->buildManifest((new PackageConfig(BASE_PATH))->all(), $this->autoDiscoverEnabled()));

        $this->success("Package created: {$pkgName}");
        $this->line("  <fg=gray>{$dir}</>");
        $this->line("  <fg=gray>Run `composer dump-autoload` to autoload it and refresh discovery.</>");
    }
}
