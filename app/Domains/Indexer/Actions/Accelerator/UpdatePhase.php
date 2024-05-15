<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\PhaseUpdated;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AccountBlock;

class UpdatePhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;
        $phase = AcceleratorPhase::findBy('hash', $blockData['id']);

        if (! $phase) {
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
    }
}
