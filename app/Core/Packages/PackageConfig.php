<?php

declare(strict_types=1);

namespace App\Core\Packages;

/**
 * Reads and mutates config/packages.php (the enable/disable source of
 * truth for ZeroPing packages).
 */
class PackageConfig
{
    public function __construct(
        private string $basePath
    ) {
    }

    public function all(): array
    {
        $file = $this->path();

        return is_file($file) ? (require $file) : [];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->all());
    }

    public function isEnabled(string $name): bool
    {
        return ($this->all()[$name] ?? false) === true;
    }

    public function set(string $name, bool $enabled): void
    {
        $all = $this->all();
        $all[$name] = $enabled;

        $this->write($all);
    }

    public function enable(string $name): void
    {
        $this->set($name, true);
    }

    public function disable(string $name): void
    {
        $this->set($name, false);
    }

    public function remove(string $name): void
    {
        $all = $this->all();

        unset($all[$name]);

        $this->write($all);
    }

    private function path(): string
    {
        return $this->basePath . '/config/packages.php';
    }

    private function write(array $data): void
    {
        $lines = "<?php\n\nreturn [\n";

        foreach ($data as $key => $value) {
            $lines .= "    '" . $key . "' => " . ($value ? 'true' : 'false') . ",\n";
        }

        $lines .= "];\n";

        file_put_contents($this->path(), $lines);
    }
}
