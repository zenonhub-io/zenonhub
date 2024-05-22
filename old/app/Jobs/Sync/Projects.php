<?php

declare(strict_types=1);

namespace App\Jobs\Sync;

use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Services\ZenonSdk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class Projects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    protected Collection $projects;

    public function handle()
    {
        try {
            $this->loadProjects();
            $this->processProjects();
        } catch (Throwable $exception) {
            Log::debug('Sync projects error - ' . $exception->getMessage());
            $this->release(30);
        }
    }

    private function loadProjects(): void
    {
        $znn = App::make(ZenonSdk::class);
        $total = null;
        $results = [];
        $page = 0;

        while (count($results) !== $total) {
            $data = $znn->accelerator->getAll($page);
            if ($data['status']) {
                if (is_null($total)) {
                    $total = $data['data']->count;
                }
                $results = array_merge($results, $data['data']->list);
            }

            $page++;
        }

        $this->projects = collect($results);
    }

    private function processProjects()
    {
        $this->projects->each(function ($data) {
            $project = AcceleratorProject::where('hash', $data->id)->first();
            if (! $project) {
                $chain = app('currentChain');
                $owner = load_account($data->owner);

                $project = AcceleratorProject::create([
                    'owner_id' => $owner->id,
                    'hash' => $data->id,
                    'name' => $data->name,
                    'slug' => Str::slug($data->name),
                    'url' => $data->url,
                    'description' => $data->description,
                    'status' => $data->status,
                    'znn_requested' => $data->znnFundsNeeded,
                    'qsr_requested' => $data->qsrFundsNeeded,
                    'total_votes' => $data->votes->total,
                    'total_yes_votes' => $data->votes->yes,
                    'total_no_votes' => $data->votes->no,
                    'modified_at' => $data->creationTimestamp,
                    'created_at' => $data->creationTimestamp,
                    'updated_at' => $data->lastUpdateTimestamp,
                ]);
            }

            $project->znn_requested = $data->znnFundsNeeded;
            $project->qsr_requested = $data->qsrFundsNeeded;
            $project->total_votes = $data->votes->total;
            $project->total_yes_votes = $data->votes->yes;
            $project->total_no_votes = $data->votes->no;
            $project->status = $data->status;
            $project->modified_at = $data->lastUpdateTimestamp;
            $project->updated_at = $data->lastUpdateTimestamp;
            $project->save();

            $this->processProjectPhases($project, $data->phases);
        });
    }

    private function processProjectPhases($project, $phases): void
    {
        foreach ($phases as $data) {
            $phase = AcceleratorPhase::where('hash', $data->phase->id)->first();
            if (! $phase) {
                $chain = app('currentChain');
                $phase = AcceleratorPhase::create([
                    'chain_id' => $chain->id,
                    'project_id' => $project->id,
                    'hash' => $data->phase->id,
                    'name' => $data->phase->name,
                    'slug' => Str::slug($data->phase->name),
                    'url' => $data->phase->url,
                    'description' => $data->phase->description,
                    'status' => $data->phase->status,
                    'znn_requested' => $data->phase->znnFundsNeeded,
                    'qsr_requested' => $data->phase->qsrFundsNeeded,
                    'total_votes' => $data->votes->total,
                    'total_yes_votes' => $data->votes->yes,
                    'total_no_votes' => $data->votes->no,
                    'accepted_at' => ($data->phase->acceptedTimestamp ?: null),
                    'created_at' => $data->phase->creationTimestamp,
                ]);

                $project->modified_at = $data->phase->creationTimestamp;
                $project->save();
            }

            $phase->total_votes = $data->votes->total;
            $phase->total_yes_votes = $data->votes->yes;
            $phase->total_no_votes = $data->votes->no;
            $phase->status = $data->phase->status;
            $phase->accepted_at = ($data->phase->acceptedTimestamp ?: null);
            $phase->save();

            $project->modified_at = $data->phase->acceptedTimestamp ?: $data->phase->creationTimestamp;
            $project->save();
        }
    }
}
