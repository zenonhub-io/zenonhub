<?php

namespace App\Console\Commands;

use App;
use Log;
use App\Classes\Indexer;
use App\Models\Nom\Momentum;
use Illuminate\Console\Command;

class Index extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes the NoM';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $znn = App::make('zenon.api');
        $indexer = new Indexer($znn);

        try {
            $momentum = $znn->ledger->getFrontierMomentum()['data'];
        } catch (\Exception $e) {
            return self::FAILURE;
        }

        $dbCount = number_format(Momentum::where('id', '>', 1)->count());
        $nodeCount = number_format($momentum->height);
        $this->info("Indexing Network of Momentum...");
        $this->line("Latest momentum height: {$nodeCount}");
        $this->line("Current database height: {$dbCount}");

        while ($momentum->height > Momentum::where('id', '>', 1)->count()) {
            try {
                $momentum = $znn->ledger->getFrontierMomentum()['data'];
                $dbCount = number_format(Momentum::where('id', '>', 1)->count());
                $nodeCount = number_format($momentum->height);
                $this->line("Processing {$dbCount} of {$nodeCount}");
                $indexer->run();
            } catch (\Exception $e) {
                Log::error($e);
                return self::FAILURE;
            }
        }

        $this->newLine();

        sleep(10);

        return self::SUCCESS;
    }
}
