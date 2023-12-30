<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateTokenPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-token-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the cached ZNN price';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Update znn price job queued');
        \App\Jobs\UpdateTokenPrices::dispatch();

        return self::SUCCESS;
    }
}
