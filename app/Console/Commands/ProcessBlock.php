<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBlock as ProcessBlockJob;
use App\Models\Nom\AccountBlock;
use Illuminate\Console\Command;

class ProcessBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-block {hash}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes the given account block hash';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $hash = $this->argument('hash');
        $accountBlock = AccountBlock::findByHash($hash);

        if ($accountBlock) {
            $this->info("Process block data job dispatched");
            ProcessBlockJob::dispatch($accountBlock);
        }

        return self::SUCCESS;
    }
}
