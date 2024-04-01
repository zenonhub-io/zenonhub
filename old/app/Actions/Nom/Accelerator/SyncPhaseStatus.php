<?php

declare(strict_types=1);

namespace App\Actions\Nom\Accelerator;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Services\ZenonSdk;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
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
            Log::warning('Unable to sync AZ phase status ' . $this->phase->hash);
            Log::debug($exception->getMessage());

            return;
        }
    }

    private function loadData(): void
    {
        $znn = App::make(ZenonSdk::class);
        $this->phaseData = $znn->accelerator->getPhaseById($this->phase->hash)['data'];
    }

    private function processData(): void
    {
        $this->phase->vote_total = $this->phaseData->votes->total;
        $this->phase->vote_yes = $this->phaseData->votes->yes;
        $this->phase->vote_no = $this->phaseData->votes->no;
        $this->phase->status = $this->phaseData->phase->status;
        $this->phase->accepted_at = ($this->phaseData->phase->acceptedTimestamp ?: null);
        $this->phase->save();
    }
}
