<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Console\Commands;

use App\Domains\Indexer\Factories\ContractMethodProcessorFactory;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class IndexContract extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'indexer:process-contract';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs the indexer for a specific contract';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $contract = $this->choice(
            'What contract should be indexed?',
            Contract::all()->pluck('name')->toArray(),
            0
        );

        $blockCount = AccountBlock::whereRelation('contractMethod.contract', 'name', $contract)->count();

        $bar = $this->output->createProgressBar($blockCount);
        $bar->start();

        AccountBlock::with('data', 'contractMethod', 'contractMethod.contract')
            ->whereRelation('contractMethod.contract', 'name', $contract)
            ->chunk(1000, function (Collection $accountBlocks) use ($bar) {
                $accountBlocks->each(function ($accountBlock) use ($bar) {
                    $blockProcessorClass = ContractMethodProcessorFactory::create($accountBlock->contractMethod);
                    $blockProcessorClass::run($accountBlock);
                    $bar->advance();
                });
            });

        $bar->finish();
        $this->info('The command was successful!');
    }

    /**
     * Prompt for missing input arguments using the returned questions.
     *
     * @return array<string, string>
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'contract' => 'Which contract should be indexed?',
        ];
    }
}
