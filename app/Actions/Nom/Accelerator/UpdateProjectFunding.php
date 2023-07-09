<?php

namespace App\Actions\Nom\Accelerator;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use Spatie\QueueableAction\QueueableAction;

class UpdateProjectFunding
{
    use QueueableAction;

    public function __construct(
        protected AcceleratorProject $project,
    ) {
    }

    public function execute(): void
    {
        $this->project->refresh();
        $this->project->znn_paid = $this->project->phases()->where('status', AcceleratorPhase::STATUS_PAID)->sum('znn_requested');
        $this->project->qsr_paid = $this->project->phases()->where('status', AcceleratorPhase::STATUS_PAID)->sum('qsr_requested');
        $this->project->znn_remaining = ($this->project->znn_requested - $this->project->znn_paid);
        $this->project->qsr_remaining = ($this->project->qsr_requested - $this->project->qsr_paid);
        $this->project->save();
    }
}
