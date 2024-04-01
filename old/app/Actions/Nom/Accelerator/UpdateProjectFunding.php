<?php

declare(strict_types=1);

namespace App\Actions\Nom\Accelerator;

use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Models\AcceleratorProject;
use Spatie\QueueableAction\QueueableAction;

class UpdateProjectFunding
{
    use QueueableAction;

    public function execute(): void
    {
        $projects = AcceleratorProject::hasRemainingFunds()
            ->isNotRejected()
            ->whereHas('phases')
            ->get();

        $projects->each(function ($project) {
            $project->refresh();
            $project->znn_paid = $project->phases()->where('status', AcceleratorPhaseStatusEnum::PAID->value)->sum('znn_requested');
            $project->qsr_paid = $project->phases()->where('status', AcceleratorPhaseStatusEnum::PAID->value)->sum('qsr_requested');
            $project->znn_remaining = ($project->znn_requested - $project->znn_paid);
            $project->qsr_remaining = ($project->qsr_requested - $project->qsr_paid);
            $project->save();
        });
    }
}
