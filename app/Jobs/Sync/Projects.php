<?php

namespace App\Jobs\Sync;

use App;
use App\Classes\Utilities;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Log;
use Str;
use Throwable;

class Projects implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    protected Collection $projects;

    public function __construct()
    {
        $this->onQueue('indexer');
    }

    public function handle()
    {
        try {
            $this->loadProjects();
            $this->processProjects();
        } catch (Throwable $exception) {
            Log::debug('Sync projects error - '.$exception->getMessage());
            $this->release(30);
        }
    }

    private function loadProjects(): void
    {
        $znn = App::make('zenon.api');
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
                $chain = Utilities::loadChain();
                $owner = Utilities::loadAccount($data->owner);

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
                    'vote_total' => $data->votes->total,
                    'vote_yes' => $data->votes->yes,
                    'vote_no' => $data->votes->no,
                    'send_reminders_at' => Carbon::createFromTimestamp($data->creationTimestamp)->addDays(13),
                    'modified_at' => $data->creationTimestamp,
                    'created_at' => $data->creationTimestamp,
                    'updated_at' => $data->lastUpdateTimestamp,
                ]);
            }

            $project->znn_requested = $data->znnFundsNeeded;
            $project->qsr_requested = $data->qsrFundsNeeded;
            $project->vote_total = $data->votes->total;
            $project->vote_yes = $data->votes->yes;
            $project->vote_no = $data->votes->no;
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
                $chain = Utilities::loadChain();
                $phase = AcceleratorPhase::create([
                    'chain_id' => $chain->id,
                    'accelerator_project_id' => $project->id,
                    'hash' => $data->phase->id,
                    'name' => $data->phase->name,
                    'slug' => Str::slug($data->phase->name),
                    'url' => $data->phase->url,
                    'description' => $data->phase->description,
                    'status' => $data->phase->status,
                    'znn_requested' => $data->phase->znnFundsNeeded,
                    'qsr_requested' => $data->phase->qsrFundsNeeded,
                    'vote_total' => $data->votes->total,
                    'vote_yes' => $data->votes->yes,
                    'vote_no' => $data->votes->no,
                    'send_reminders_at' => Carbon::createFromTimestamp($data->phase->creationTimestamp)->addDays(13),
                    'accepted_at' => ($data->phase->acceptedTimestamp ?: null),
                    'created_at' => $data->phase->creationTimestamp,
                ]);

                $project->modified_at = $data->phase->creationTimestamp;
                $project->save();
            }

            $phase->vote_total = $data->votes->total;
            $phase->vote_yes = $data->votes->yes;
            $phase->vote_no = $data->votes->no;
            $phase->status = $data->phase->status;
            $phase->accepted_at = ($data->phase->acceptedTimestamp ?: null);
            $phase->save();

            $project->modified_at = $data->phase->acceptedTimestamp ?: $data->phase->creationTimestamp;
            $project->save();
        }
    }
}
