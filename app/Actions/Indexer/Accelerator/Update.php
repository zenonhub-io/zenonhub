<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Accelerator;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Enums\Nom\AcceleratorProjectStatusEnum;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use Illuminate\Support\Facades\Log;

class Update extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        //        try {
        //            $this->validateAction($accountBlock);
        //        } catch (IndexerActionValidationException $e) {
        //            Log::error('Contract Method Processor - Accelerator: Donate failed', [
        //                'accountBlock' => $accountBlock->hash,
        //                'blockData' => $blockData,
        //                'error' => $e->getMessage(),
        //            ]);
        //
        //            return;
        //        }

        $projects = AcceleratorProject::with('phases')
            ->whereIn('status', [
                AcceleratorProjectStatusEnum::NEW->value,
                AcceleratorProjectStatusEnum::ACCEPTED->value,
            ])->get();

        $projects->sortBy(fn (AcceleratorProject $project) => $project->phases->last()?->created_at->timestamp ?: $project->created_at->timestamp);

        $projects->each(function (AcceleratorProject $project) use ($accountBlock) {
            if ($project->status === AcceleratorProjectStatusEnum::NEW) {
                if (! $project->getIsVotingOpenAttribute($accountBlock->created_at)) {

                    $project->status = ($project->is_quorum_reached && $project->total_yes_votes > $project->total_no_votes)
                        ? AcceleratorProjectStatusEnum::ACCEPTED
                        : AcceleratorProjectStatusEnum::REJECTED;

                    $project->updated_at = $accountBlock->created_at;
                    $project->save();
                }
            } elseif ($project->status === AcceleratorProjectStatusEnum::ACCEPTED) {

                $phase = $project->phases()->latest()->first();

                if (! $phase) {
                    return;
                }

                if (
                    $phase->status === AcceleratorPhaseStatusEnum::OPEN &&
                    $phase->is_quorum_reached &&
                    $phase->total_yes_votes > $phase->total_no_votes
                ) {
                    $phase->status = AcceleratorPhaseStatusEnum::PAID;
                    $phase->updated_at = $accountBlock->created_at;
                    $phase->save();

                    $project->znn_paid += $phase->znn_requested;
                    $project->qsr_paid += $phase->qsr_requested;
                    $project->znn_remaining -= $phase->znn_requested;
                    $project->qsr_remaining -= $phase->qsr_requested;
                    $project->save();
                }

                if ($project->znn_remaining === 0 && $project->qsr_remaining === 0) {
                    $project->status = AcceleratorProjectStatusEnum::COMPLETE;
                    $project->updated_at = $accountBlock->created_at;
                    $project->save();
                }
            }
        });

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): void
    {
        [$accountBlock] = func_get_args();
    }
}
