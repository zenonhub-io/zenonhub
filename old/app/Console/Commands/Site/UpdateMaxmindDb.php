<?php

namespace App\Console\Commands\Site;

use Illuminate\Console\Command;

class UpdateMaxmindDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:update-maxmind-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the maxmind IP database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Update maxmind db job queued');
        \App\Jobs\UpdateMaxmindDb::dispatch();

        return self::SUCCESS;
    }
}
