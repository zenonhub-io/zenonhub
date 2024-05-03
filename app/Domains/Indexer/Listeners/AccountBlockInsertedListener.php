<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Listeners;

use App\Domains\Indexer\Events\AccountBlockInserted;
use App\Domains\Indexer\Factories\ContractMethodProcessorFactory;
use App\Domains\Nom\Models\AccountBlock;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;

class AccountBlockInsertedListener implements ShouldQueue
{
    public string $queue = 'indexer';

    /**
     * Handle the event.
     */
    public function handle(AccountBlockInserted $event): void
    {
        $accountBlock = $event->accountBlock;
        $this->dispatchContractMethodProcessor($accountBlock);
    }

    private function dispatchContractMethodProcessor(AccountBlock $accountBlock): void
    {
        if (! $accountBlock->contractMethod) {
            return;
        }

        try {
            $jobDelay = now()->addMinute()->diffInSeconds(now());
            $blockProcessorClass = ContractMethodProcessorFactory::create($accountBlock->contractMethod);
            $blockProcessorClass::dispatch($accountBlock)
                ->onQueue('blockProcessor')
                ->delay($jobDelay);
        } catch (Exception $exception) {
        }
    }
}
