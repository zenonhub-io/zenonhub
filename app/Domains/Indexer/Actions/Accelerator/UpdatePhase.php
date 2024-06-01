<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Accelerator\PhaseUpdated;
use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class UpdatePhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $project = AcceleratorProject::firstWhere('hash', $blockData['id']);
        $phase = $project?->phases()->latest()->first();

        if (! $project || ! $phase || ! $this->validateAction($accountBlock, $project, $phase)) {
            Log::info('Contract Method Processor - Accelerator: UpdatePhase failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $phase->hash = $accountBlock->hash;
        $phase->name = $blockData['name'];
        $phase->description = $blockData['description'];
        $phase->url = $blockData['url'];
        $phase->znn_requested = $blockData['znnFundsNeeded'];
        $phase->qsr_requested = $blockData['qsrFundsNeeded'];
        $phase->updated_at = $accountBlock->created_at;
        $phase->save();

        $phase->votes()->delete();

        $phase->project->updated_at = $accountBlock->created_at;
        $phase->project->save();

        PhaseUpdated::dispatch($accountBlock, $phase);

        Log::info('Contract Method Processor - Accelerator: UpdatePhase complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'phase' => $phase,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var AcceleratorProject $project
         * @var AcceleratorPhase $phase
         */
        [$accountBlock, $project, $phase] = func_get_args();

        if ($project->owner_id !== $accountBlock->account_id) {
            return false;
        }

        if (! $phase || $phase->status !== AcceleratorPhaseStatusEnum::OPEN) {
            return false;
        }

        return true;
    }
}
