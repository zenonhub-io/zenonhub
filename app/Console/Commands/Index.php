<?php

namespace App\Console\Commands;

use App;
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
    protected $signature = 'zenon:index {height?} {--auto=false} {--alerts=false} {--balances=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexes the Network of Momentum';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $height = $this->argument('height');
        $auto = $this->option('auto');
        $whaleAlerts = $this->option('alerts');
        $balances = $this->option('balances');

        if ($auto) {
            $auto = filter_var($auto, FILTER_VALIDATE_BOOLEAN);
        }

        if ($whaleAlerts) {
            $whaleAlerts = filter_var($whaleAlerts, FILTER_VALIDATE_BOOLEAN);
        }

        if ($balances) {
            $balances = filter_var($balances, FILTER_VALIDATE_BOOLEAN);
        }

        try {
            $znn = App::make('zenon.api');
            $momentum = $znn->ledger->getFrontierMomentum()['data'];
        } catch (\Exception $e) {
            return self::FAILURE;
        }

        if (! $height) {
            $height = Momentum::max('height');

            // Re-sync last momentum to be safe but ignore genesis
            if ($height > 2) {
                $height--;
            } else {
                $height = 2;
            }
        }

        $networkHeight = number_format($momentum->height);
        $startHeight = number_format($height);
        $this->info('Indexing Network of Momentum...');
        $this->info("Network height {$networkHeight}");
        $this->info("Start height {$startHeight}");
        $this->line('Processing...');

        $indexer = new Indexer($znn, $auto, $height, $whaleAlerts, $balances);
        $indexer->run();

        return self::SUCCESS;
    }
}
