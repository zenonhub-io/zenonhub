<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

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
        \App\Jobs\GenerateSitemap::dispatch();
        $this->info("Sitemap job queued");
        return self::SUCCESS;
    }
}
