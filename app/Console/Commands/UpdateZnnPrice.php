<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateZnnPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:update-znn-price';

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
        \App\Jobs\UpdateZnnPrice::dispatch();

        return self::SUCCESS;
    }
}
