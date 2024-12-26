<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Events\Indexer\Accelerator\PhaseUpdated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Facades\Log;

class UpdatePhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $project = AcceleratorProject::firstWhere('hash', $blockData['id']);
        $phase = $project?->phases()->latest()->first();

        try {
            $this->validateAction($accountBlock, $project, $phase);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Accelerator: UpdatePhase failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
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

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         * @var AcceleratorProject $project
         * @var AcceleratorPhase $phase
         */
        [$accountBlock, $project, $phase] = func_get_args();

        if (! $project) {
            throw new IndexerActionValidationException('Invalid project');
        }

        if (! $phase) {
            throw new IndexerActionValidationException('Invalid phase');
        }

        if ($project->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not project owner');
        }

        if ($phase->status !== AcceleratorPhaseStatusEnum::OPEN) {
            throw new IndexerActionValidationException('Latest phase is not open');
        }
    }
}
