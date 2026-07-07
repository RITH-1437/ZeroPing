<?php

namespace App\Core\Console\Commands;

use App\Core\Console\Command;

class MakeMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'make:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Create a new mailable class';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(string $name): void
    {
        if (empty($name)) {
            echo "Usage: php zero make:mail MailableName\n";
            return;
        }

        $this->createMailable($name);
        $this->createView($name);

        $this->info("Mailable created successfully.");
    }

    protected function createMailable(string $name): void
    {
        $content = $this->replace(
            $this->stub('mailable.stub'),
            ['class' => $name]
        );

        $file = BASE_PATH . "/app/Mail/{$name}.php";

        $this->write($file, $content);
    }

    protected function createView(string $name): void
    {
        $view = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $name));

        $file = BASE_PATH . "/views/emails/{$view}.php";

        $this->write($file, '');
    }
}
