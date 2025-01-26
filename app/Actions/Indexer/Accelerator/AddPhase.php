<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Accelerator\PhaseCreated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AddPhase extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $project = AcceleratorProject::firstWhere('hash', $blockData['id']);

        try {
            $this->validateAction($accountBlock, $project);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Accelerator: AddPhase failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $znnPrice = app('znnToken')->price;
        $qsrPrice = app('qsrToken')->price;

        $phase = $project->phases()->create([
            'hash' => $accountBlock->hash,
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
            'url' => $blockData['url'],
            'description' => $blockData['description'],
            'phase_number' => $project->phases()->max('phase_number') + 1,
            'znn_requested' => $blockData['znnFundsNeeded'],
            'qsr_requested' => $blockData['qsrFundsNeeded'],
            'znn_price' => $znnPrice ?: null,
            'qsr_price' => $qsrPrice ?: null,
            'created_at' => $accountBlock->created_at,
        ]);

        $project->updated_at = $accountBlock->created_at;
        $project->save();

        PhaseCreated::dispatch($accountBlock, $phase);

        Log::info('Contract Method Processor - Accelerator: AddPhase complete', [
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
         */
        [$accountBlock, $project] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $project) {
            throw new IndexerActionValidationException('Invalid project');
        }

        $latestPhase = $project->phases()->latest()->first();

        if ($project->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not project owner');
        }

        //        if ($project->status !== AcceleratorProjectStatusEnum::ACCEPTED) {
        //            throw new IndexerActionValidationException('Project has not been accepted');
        //        }
        //
        //        if ($latestPhase && $latestPhase->status !== AcceleratorPhaseStatusEnum::PAID) {
        //            throw new IndexerActionValidationException('Latest phase has not been paid');
        //        }

        if ($blockData['name'] === '' || strlen($blockData['name']) > config('nom.accelerator.projectNameLengthMax')) {
            throw new IndexerActionValidationException('Invalid name');
        }

        if ($blockData['description'] === '' || strlen($blockData['description']) > config('nom.accelerator.projectDescriptionLengthMax')) {
            throw new IndexerActionValidationException('Invalid description');
        }

        if ($blockData['znnFundsNeeded'] > config('nom.accelerator.projectZnnMaximumFunds')) {
            throw new IndexerActionValidationException('Max ZNN fund exceeded');
        }

        if ($blockData['qsrFundsNeeded'] > config('nom.accelerator.projectQsrMaximumFunds')) {
            throw new IndexerActionValidationException('Max QSR funds exceeded');
        }
    }
}
