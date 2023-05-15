<?php

namespace App\Console\Commands;

use App\Models\Nom\AccountBlock;
use Illuminate\Console\Command;

class ProcessBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-block {hash} {--alerts=false} {--balances=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes the given account block hash';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $hash = $this->argument('hash');
        $whaleAlerts = $this->option('alerts');
        $balances = $this->option('balances');
        $block = AccountBlock::findByHash($hash);

        if ($whaleAlerts) {
            $whaleAlerts = filter_var($whaleAlerts, FILTER_VALIDATE_BOOLEAN);
        }

        if ($balances) {
            $balances = filter_var($balances, FILTER_VALIDATE_BOOLEAN);
        }

        if ($block) {
            $this->info('Process block data job dispatched');

            (new \App\Actions\ProcessBlock(
                $block,
                $whaleAlerts,
                $balances
            ))->execute();
        }

        return self::SUCCESS;
    }
}
