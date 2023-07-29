<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessUnprocessedBlocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zenon:process-unrpocessed-blocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocesses blocks with missing decoded data';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Process block data');
        (new \App\Actions\ProcessUnprocessedBlocks())->execute();

        return self::SUCCESS;
    }
}
