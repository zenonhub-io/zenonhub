<?php

declare(strict_types=1);

namespace App\Console\Commands\OneOff;

use App\Factories\ContractMethodProcessorFactory;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use Illuminate\Console\Command;

class FixMissingMints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'one-off:fix-missing-mints';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes missing mint transactions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing missing mints');

        $contractMethod = ContractMethod::firstWhere('name', 'Mint');
        $blocks = AccountBlock::with('contractMethod', 'data')
            ->where('contract_method_id', $contractMethod->id)
            ->whereRelation('data', 'is_processed', false)
            ->get();

        $blocks->each(function (AccountBlock $block) {
            $blockProcessorClass = ContractMethodProcessorFactory::create($block->contractMethod);
            $blockProcessorClass::run($block);
        });

        $this->info('Done');

        return self::SUCCESS;
    }
}
