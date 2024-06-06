<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\DataTransferObjects\AcceleratorPhaseDTO;
use App\Domains\Nom\DataTransferObjects\AcceleratorProjectDTO;
use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Services\ZenonSdk;
use Lorisleiva\Actions\Concerns\AsAction;

class SyncProjectStatus
{
    use AsAction;

    public function handle(AcceleratorProject $project): void
    {
        try {
            $projectDTO = app(ZenonSdk::class)
                ->getProjectById($project->hash);
        } catch (ZenonRpcException $e) {
            return;
        }

        $this->syncProjectStatus($project, $projectDTO);

        $projectDTO->phases->each(function (AcceleratorPhaseDTO $phaseDTO) use ($project) {

            $phase = $project->phases()
                ->firstWhere('hash', $phaseDTO->phase->id);

            if (! $phase) {
                return;
            }

            $this->syncPhaseStatus($phase, $phaseDTO);
        });
    }

    private function syncProjectStatus(AcceleratorProject $project, AcceleratorProjectDTO $projectDTO): void
    {
        $project->total_votes = $projectDTO->votes->total;
        $project->total_yes_votes = $projectDTO->votes->yes;
        $project->total_no_votes = $projectDTO->votes->no;
        $project->total_abstain_votes = $project->total_votes - ($project->total_yes_votes + $project->total_no_votes);
        $project->status = $projectDTO->status;
        $project->updated_at = $projectDTO->lastUpdateTimestamp;
        $project->save();
    }

    private function syncPhaseStatus(AcceleratorPhase $phase, AcceleratorPhaseDTO $phaseDTO): void
    {
        $originalStatus = $phase->status;

        $phase->total_votes = $phaseDTO->votes->total;
        $phase->total_yes_votes = $phaseDTO->votes->yes;
        $phase->total_no_votes = $phaseDTO->votes->no;
        $phase->total_abstain_votes = $phase->total_votes - ($phase->total_yes_votes + $phase->total_no_votes);
        $phase->status = $phaseDTO->phase->status;
        $phase->save();

        if ($phase->status === AcceleratorPhaseStatusEnum::PAID && $originalStatus !== $phase->status) {
            $phase->project->znn_paid += $phase->znn_requested;
            $phase->project->qsr_paid += $phase->qsr_requested;
            $phase->project->znn_remaining -= $phase->znn_requested;
            $phase->project->qsr_remaining -= $phase->qsr_requested;
            $phase->project->save();
        }
    }
}
