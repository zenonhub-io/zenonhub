<?php

namespace App\Actions\Nom\Accelerator;

use App;
use App\Models\Nom\AcceleratorPhase;
use Exception;
use Log;
use Spatie\QueueableAction\QueueableAction;

class SyncPhaseStatus
{
    use QueueableAction;

    protected ?object $phaseData;

    protected ?object $projectData;

    public function __construct(
        protected AcceleratorPhase $phase,
    ) {
    }

    public function execute(): void
    {
        try {
            $this->loadData();
            $this->processData();
        } catch (Exception $exception) {
            Log::warning('Unable to sync AZ phase status '.$this->phase->hash);
            Log::debug($exception->getMessage());

            return;
        }
    }

    private function loadData(): void
    {
        $znn = App::make('zenon.api');
        $this->phaseData = $znn->accelerator->getPhaseById($this->phase->hash)['data'];
        $this->projectData = $znn->accelerator->getProjectById($this->phase->project->hash)['data'];
    }

    private function processData(): void
    {
        $this->phase->vote_total = $this->phaseData->votes->total;
        $this->phase->vote_yes = $this->phaseData->votes->yes;
        $this->phase->vote_no = $this->phaseData->votes->no;
        $this->phase->status = $this->phaseData->status;
        $this->phase->accepted_at = ($this->phaseData->acceptedTimestamp ?: null);
        $this->phase->save();

        $this->phase->project->modified_at = $this->projectData->lastUpdateTimestamp;
        $this->phase->project->updated_at = $this->projectData->lastUpdateTimestamp;
        $this->phase->project->save();
    }
}
