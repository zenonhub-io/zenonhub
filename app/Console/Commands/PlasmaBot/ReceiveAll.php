<?php

namespace App\Console\Commands\PlasmaBot;

use Illuminate\Console\Command;

class ReceiveAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plasma-bot:receive-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receives all unreceived transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Receiving all transactions...');

        (new \App\Actions\PlasmaBot\ReceiveAll())->execute();

        return self::SUCCESS;
    }
}
