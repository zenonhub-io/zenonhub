<?php

namespace App\Console\Commands\PlasmaBot;

use Illuminate\Console\Command;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plasma-bot:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the plasma bot';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Setting up bot...');

        (new \App\Actions\PlasmaBot\Install())->execute();

        return self::SUCCESS;
    }
}
