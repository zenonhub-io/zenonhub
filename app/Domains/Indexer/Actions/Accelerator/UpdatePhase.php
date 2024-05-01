<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AccountBlock;

class UpdatePhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->updatePhase();
    }

    private function updatePhase()
    {
        $blockData = $this->accountBlock->data->decoded;

        $phase = AcceleratorPhase::where('hash', $blockData['id'])->first();

        if ($phase) {
            $phase->name = $blockData['name'];
            $phase->description = $blockData['description'];
            $phase->url = $blockData['url'];
            $phase->znn_requested = $blockData['znnFundsNeeded'];
            $phase->qsr_requested = $blockData['qsrFundsNeeded'];
            $phase->updated_at = $this->accountBlock->momentum->created_at;
            $phase->save();

            $phase->project->modified_at = $this->accountBlock->momentum->created_at;
            $phase->project->save();
        }
    }
}
