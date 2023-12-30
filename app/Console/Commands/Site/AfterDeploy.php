<?php

namespace App\Console\Commands\Site;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AfterDeploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:after-deploy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs all our after deploy processes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Running deployment scripts');
        $this->call('config:cache');
        $this->call('event:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('zenon:update-contract-methods');
        $this->call('zenon:update-named-addresses');
        $this->call('zenon:update-token-prices');
        $this->call('zenon:sync', [
            'orchestrators',
            'nodes',
        ]);
        $this->call('site:create-caches');
        $this->call('site:generate-sitemap');

        Log::debug('Running after deploy scripts');

        return self::SUCCESS;
    }
}
