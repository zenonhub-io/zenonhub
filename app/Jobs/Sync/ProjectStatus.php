<?php

namespace App\Jobs\Sync;

use App;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class ProjectStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    protected AcceleratorProject $project;

    protected mixed $projectData;

    public function __construct(AcceleratorProject $project)
    {
        $this->project = $project;
    }

    public function handle(): void
    {
        try {
            $this->loadProject();
            $this->processProject();
            $this->processPhases();
        } catch (Throwable $exception) {
            Log::debug('Sync project status error - '.$exception);
            $this->release(30);
        }
    }

    private function loadProject(): void
    {
        $znn = App::make('zenon.api');
        $this->projectData = $znn->accelerator->getProjectById($this->project->hash)['data'];
    }

    private function processProject(): void
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
        $znn = App::make('zenon.api');
        foreach ($this->projectData->phaseIds as $phaseId) {
            $phase = $this->project->phases()->where('hash', $phaseId)
                ->where('status', App\Models\Nom\AcceleratorPhase::STATUS_OPEN)
                ->first();

            if (! $phase) {
                continue;
            }

            $phaseData = $znn->accelerator->getPhaseById($phaseId)['data'];
            $phase->vote_total = $phaseData->votes->total;
            $phase->vote_yes = $phaseData->votes->yes;
            $phase->vote_no = $phaseData->votes->no;
            $phase->accepted_at = ($phaseData->phase->acceptedTimestamp ?: null);
            $phase->save();
        }
    }
}
