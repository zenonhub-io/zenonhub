<?php

namespace App\Console\Commands;

use App\Models\Nom\AccountBlock;
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

        $baseQuery = AccountBlock::whereNotNull('contract_method_id')
            ->where('block_type', [AccountBlock::TYPE_SEND, AccountBlock::TYPE_CONTRACT_SEND])
            ->whereHas('data',
                fn ($q) => $q->whereNotNull('raw')
                    ->whereNull('decoded')
                    ->whereNotIn('raw', ['AAAAAAAAAAE=', 'AAAAAAAAAAI=', 'IAk+pg==', 'y3+LKg==', 's9ZY/Q==', 'r0PT8A==', 'OhbyDg==', '+kuhXw=='])
            );

        $this->output->progressStart($baseQuery->count());

        $baseQuery->chunkById(200, function ($blocks) {
            foreach ($blocks as $block) {
                (new \App\Actions\SaveBlockAbi($block))->execute();
                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();

        return self::SUCCESS;
    }
}
