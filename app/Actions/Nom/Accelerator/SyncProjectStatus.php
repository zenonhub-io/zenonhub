<?php

namespace App\Actions\Nom\Accelerator;

use App\Models\Nom\AcceleratorProject;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\QueueableAction\QueueableAction;

class SyncProjectStatus
{
    use QueueableAction;

    protected ?object $projectData;

    public function __construct(
        protected AcceleratorProject $project
    ) {
    }

    public function execute(): void
    {
        try {
            $this->loadData();
            $this->processData();
            $this->processPhases();
        } catch (Exception $exception) {
            Log::warning('Unable to sync AZ project status '.$this->project->hash);
            Log::debug($exception->getMessage());
        }
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

    private function processPhases(): void
    {
        $this->project->phases->each(function ($phase) {
            (new SyncPhaseStatus($phase))->execute();
        });
    }
}
