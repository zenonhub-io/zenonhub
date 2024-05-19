<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\PhaseUpdated;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class UpdatePhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $phase = AcceleratorPhase::findBy('hash', $blockData['id']);

        if (! $phase || ! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: UpdatePhase failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $phase->name = $blockData['name'];
        $phase->description = $blockData['description'];
        $phase->url = $blockData['url'];
        $phase->znn_requested = $blockData['znnFundsNeeded'];
        $phase->qsr_requested = $blockData['qsrFundsNeeded'];
        $phase->updated_at = $accountBlock->created_at;
        $phase->save();

        $phase->votes()->delete();

        $phase->project->modified_at = $accountBlock->created_at;
        $phase->project->save();

        PhaseUpdated::dispatch($accountBlock, $phase);

        Log::info('Contract Method Processor - Accelerator: UpdatePhase failed', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'phase' => $phase,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    protected function validateAction(): bool
    {
        [$accountBlock] = func_get_args();

        return true;
    }
}
