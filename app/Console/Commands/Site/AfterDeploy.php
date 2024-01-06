<?php

namespace App\Console\Commands\Site;

use App\Actions\CreateCaches;
use App\Actions\GenerateSitemap;
use App\Actions\UpdateContractMethods;
use App\Actions\UpdateTokenPrices;
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
        $this->call('zenon:update-named-addresses');
        $this->call('zenon:sync', [
            'orchestrators',
            'nodes',
        ]);

        (new CreateCaches())->execute();
        (new UpdateContractMethods())->execute();
        (new UpdateTokenPrices())->execute();
        (new GenerateSitemap())->execute();

        Log::debug('Running after deploy scripts');

        return self::SUCCESS;
    }
}
