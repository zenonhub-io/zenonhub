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
    protected $signature = 'zenon:process-block {hash} {--alerts=false}';

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
        $alerts = $this->option('alerts');
        $block = AccountBlock::findByHash($hash);

        if ($alerts) {
            $alerts = filter_var($alerts, FILTER_VALIDATE_BOOLEAN);
        }
        if ($block) {
            $this->info('Process block data job dispatched');

            (new \App\Actions\ProcessBlock(
                $block,
                $alerts
            ))->execute();
        }

        return self::SUCCESS;
    }
}
