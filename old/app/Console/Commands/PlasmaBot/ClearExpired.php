<?php

namespace App\Console\Commands\PlasmaBot;

use Illuminate\Console\Command;

class ClearExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plasma-bot:clear-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears our expired fuses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Clearing expired entries...');

        (new \App\Actions\PlasmaBot\CancelExpired())->execute();

        return self::SUCCESS;
    }
}
