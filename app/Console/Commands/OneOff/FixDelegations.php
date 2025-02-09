<?php

declare(strict_types=1);

namespace App\Console\Commands\OneOff;

use App\Factories\ContractMethodProcessorFactory;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixDelegations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'one-off:fix-delegations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes inaccurate delegations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Processing missing delegations');

        DB::table('nom_delegations')->truncate();

        $contractMethods = ContractMethod::whereIn('name', ['Delegate', 'Undelegate'])->get();
        AccountBlock::with('contractMethod', 'contractMethod.contract', 'data')
            ->whereIn('contract_method_id', $contractMethods->pluck('id'))
            ->chunk(500, function ($blocks) {
                $blocks->each(function (AccountBlock $block) {
                    $blockProcessorClass = ContractMethodProcessorFactory::create($block->contractMethod);
                    $blockProcessorClass::run($block);
                });
            });

        $this->info('Done');

        return self::SUCCESS;
    }
}
