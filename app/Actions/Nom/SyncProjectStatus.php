<?php

namespace App\Actions\Nom;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Support\Facades\App;
use Spatie\QueueableAction\QueueableAction;

class SyncProjectStatus
{
    use QueueableAction;

    public function __construct(
        protected AcceleratorProject $project,
        protected array $projectData
    ) {
    }

    public function execute()
    {
        $this->loadData();
        $this->processData();
    }

    private function loadData(): void
    {
        $znn = App::make('zenon.api');
        $this->projectData = $znn->accelerator->getProjectById($this->project->hash)['data'];
    }

    private function processData(): void
    {
        $this->project->vote_total = $this->projectData->votes->total;
        $this->project->vote_yes = $this->projectData->votes->yes;
        $this->project->vote_no = $this->projectData->votes->no;
        $this->project->status = $this->projectData->status;
        $this->project->modified_at = $this->projectData->lastUpdateTimestamp;
        $this->project->updated_at = $this->projectData->lastUpdateTimestamp;
        $this->project->save();
    }
}
