<?php

namespace App\Actions\Nom;

use App\Models\Nom\AcceleratorPhase;
use Exception;
use Illuminate\Support\Facades\App;
use Log;
use Spatie\QueueableAction\QueueableAction;

class SyncPhaseStatus
{
    use QueueableAction;

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
            Log::error('Unable to sync AZ phase status '.$this->phase->hash);
            Log::error($exception->getMessage());

            return;
        }
    }

    private function loadData(): void
    {
        $znn = App::make('zenon.api');
        $this->projectData = $znn->accelerator->getProjectById($this->phase->hash)['data'];
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
