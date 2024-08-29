<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Indexer;
use Illuminate\Console\Command;

class Index extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexer:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the Indexer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $indexer = app(Indexer::class);
        $indexer->run();
    }
}
