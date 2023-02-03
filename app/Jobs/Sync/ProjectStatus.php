<?php

namespace App\Jobs\Sync;

use App;
use Log;
use Throwable;
use App\Models\Nom\AcceleratorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProjectStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
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
        } catch (\DigitalSloth\ZnnPhp\Exceptions\Exception) {
            Log::error('Sync project status error - could not load data from API');
            $this->release(10);
        } catch (Throwable $exception) {
            Log::error('Sync project status error - ' . $exception);
            $this->release(10);
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
}
