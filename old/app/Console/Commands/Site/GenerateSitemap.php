<?php

namespace App\Console\Commands\Site;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'site:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates builds a new xml sitemap';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new \App\Actions\GenerateSitemap())->execute();

        return self::SUCCESS;
    }
}
