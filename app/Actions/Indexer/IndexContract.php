<?php

declare(strict_types=1);

namespace App\Actions\Indexer;

use App\Exceptions\ContractMethodProcessorNotFound;
use App\Factories\ContractMethodProcessorFactory;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class IndexContract
{
    use AsAction;

    public string $commandSignature = 'indexer:index-contract';

    public function handle(Contract $contract): void
    {
        $blockCount = AccountBlock::whereRelation('contractMethod', 'contract_id', $contract->id)->count();

        $progressBar = new ProgressBar(new ConsoleOutput, $blockCount);
        $progressBar->start();

        AccountBlock::with('data', 'contractMethod', 'contractMethod.contract')
            ->whereRelation('contractMethod.contract', 'name', $contract->name)
            ->chunk(1000, function (Collection $accountBlocks) use ($progressBar) {
                $accountBlocks->each(function ($accountBlock) use ($progressBar) {
                    try {
                        $blockProcessorClass = ContractMethodProcessorFactory::create($accountBlock->contractMethod);
                        $blockProcessorClass::run($accountBlock);
                    } catch (ContractMethodProcessorNotFound $e) {
                    }

                    $progressBar->advance();
                });
            });

        $progressBar->finish();
    }

    public function asCommand(Command $command): void
    {
        $contractName = $command->choice(
            'What contract should be indexed?',
            Contract::all()->pluck('name')->toArray(),
            0
        );

        $contract = Contract::firstWhere('name', $contractName);

        $this->handle($contract);
    }
}
