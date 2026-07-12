<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;
use App\Core\Application\App;

class AboutCommand extends Command
{
    protected string $signature = 'about';

    protected string $description = 'Display framework information';

    public function handle(): void
    {
        $this->title('About ZeroPing');

        $this->line('  <fg=white>Framework</>      <fg=cyan>ZeroPing v' . App::VERSION . '</>');
        $this->line('  <fg=white>PHP</>            <fg=cyan>' . PHP_VERSION . '</>');
        $this->line('  <fg=white>Server</>         <fg=cyan>' . php_sapi_name() . '</>');
        $this->line('  <fg=white>OS</>             <fg=cyan>' . php_uname('s') . ' ' . php_uname('r') . '</>');
        $this->line('  <fg=white>Environment</>    <fg=cyan>' . ($_ENV['APP_ENV'] ?? 'local') . '</>');
        $this->line('  <fg=white>Debug</>          <fg=cyan>' . ($_ENV['APP_DEBUG'] ?? 'false') . '</>');
        $this->line('');
        $this->line('  <fg=gray>Repository: https://github.com/RITH-1437/ZeroPing</>');
        $this->line('');
    }
}
