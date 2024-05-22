<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use Illuminate\Support\Facades\Log;

class Update extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data?->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: Donate failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $projects = AcceleratorProject::with('phases')
            ->whereIn('status', [
                AcceleratorProjectStatusEnum::NEW->value,
                AcceleratorProjectStatusEnum::ACCEPTED->value,
            ])->get();

        $projects->sortBy(fn (AcceleratorProject $project) => $project->phases->last()?->created_at->timestamp ?: $project->created_at->timestamp);

        $projects->each(function (AcceleratorProject $project) use ($accountBlock) {

            if ($project->status === AcceleratorProjectStatusEnum::NEW) {
                if (! $project->getIsVotingOpenAttribute($accountBlock->created_at)) {
                    if ($project->total_yes_votes > $project->total_no_votes) {
                        $project->status = AcceleratorProjectStatusEnum::ACCEPTED;
                    } else {
                        $project->status = AcceleratorProjectStatusEnum::REJECTED;
                    }
                }
            } elseif ($project->status === AcceleratorProjectStatusEnum::ACCEPTED) {

                $phase = $project->phases()->latest()->first();

                if (! $phase) {
                    return;
                }

                if ($phase->status === AcceleratorPhaseStatusEnum::OPEN && $phase->is_quorum_reached) {
                    if ($phase->total_yes_votes > $phase->total_no_votes) {
                        $phase->status = AcceleratorPhaseStatusEnum::PAID;
                        $phase->updated_at = $accountBlock->created_at;
                        $phase->save();

                        $project->znn_paid += $phase->znn_requested;
                        $project->qsr_paid += $phase->qsr_requested;
                        $project->znn_remaining -= $phase->znn_requested;
                        $project->qsr_remaining -= $phase->qsr_requested;
                        $project->save();
                    }
                }

                if ($project->znn_remaining && $project->qsr_remaining) {
                    $project->status = AcceleratorProjectStatusEnum::COMPLETE;
                    $project->updated_at = $accountBlock->created_at;
                    $project->save();
                }
            }
        });

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        [$accountBlock] = func_get_args();

        return true;
    }
}
