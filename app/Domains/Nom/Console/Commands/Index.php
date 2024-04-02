<?php

declare(strict_types=1);

namespace App\Domains\Nom\Console\Commands;

use App\Domains\Nom\Services\Indexer;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class Index extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nom:index';

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
