<?php

namespace App\Actions\Nom;

use App\Models\Nom\AcceleratorPhase;
use Illuminate\Support\Facades\App;
use Spatie\QueueableAction\QueueableAction;

class SyncPhaseStatus
{
    use QueueableAction;

    public function __construct(
        protected AcceleratorPhase $phase,
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
        $this->phase->vote_total = $this->projectData->votes->total;
        $this->phase->vote_yes = $this->projectData->votes->yes;
        $this->phase->vote_no = $this->projectData->votes->no;
        $this->phase->accepted_at = ($this->projectData->phase->acceptedTimestamp ?: null);
        $this->phase->save();
    }
}
