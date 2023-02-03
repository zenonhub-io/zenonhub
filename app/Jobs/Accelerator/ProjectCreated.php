<?php

namespace App\Jobs\Accelerator;

use App;
use Str;
use Notification;
use App\Classes\Utilities;
use App\Models\User;
use App\Models\NotificationType;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProjectCreated implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $projectData = $this->getProjectData();

        if (! $projectData) {
            return;
        }

        $project = $this->saveProject($projectData);
        $this->notifyUsers($project);
    }

    private function getProjectData()
    {
        $projects = $this->loadAllProjects();
        $existingProjects = AcceleratorProject::pluck('hash')->toArray();

        // Find projects project data
        foreach ($projects as $data) {
            if (! in_array($data->id, $existingProjects)) {
                return $data;
            }
        }

        return null;
    }

    private function loadAllProjects(): array
    {
        $znn = App::make('zenon.api');
        $total = null;
        $projects = [];
        $page = 0;

        // Load all projects from the network
        while (count($projects) !== $total) {
            $results = $znn->accelerator->getAll($page);
            if ($results['status']) {
                if (is_null($total)) {
                    $total = $results['data']->count;
                }
                $projects = array_merge($projects, $results['data']->list);
            }

            $page++;
        }

        return $projects;
    }

    private function saveProject($projectData): AcceleratorProject
    {
        $znnPrice = App::make('coingeko.api')->historicPrice('zenon', 'usd', $projectData->creationTimestamp);

        if (! $znnPrice) {
            $this->release(5);
        } else {

            $project = AcceleratorProject::where('hash', $projectData->id)->first();
            $qsrPrice = (float) $znnPrice / 10;

            if (! $project) {
                $owner = Utilities::loadAccount($projectData->owner);
                $project = AcceleratorProject::create([
                    'owner_id' => $owner->id,
                    'hash' => $projectData->id,
                    'name' => $projectData->name,
                    'slug' => Str::slug($projectData->name),
                    'url' => $projectData->url,
                    'description' => $projectData->description,
                    'status' => $projectData->status,
                    'znn_funds_needed' => $projectData->znnFundsNeeded,
                    'qsr_funds_needed' => $projectData->qsrFundsNeeded,
                    'znn_price' => $znnPrice,
                    'qsr_price' => $qsrPrice,
                    'vote_total' => $projectData->votes->total,
                    'vote_yes' => $projectData->votes->yes,
                    'vote_no' => $projectData->votes->no,
                    'send_reminders_at' => Carbon::parse($projectData->creationTimestamp)->addDays(13),
                    'modified_at' => $projectData->creationTimestamp,
                    'updated_at' => $projectData->lastUpdateTimestamp,
                    'created_at' => $projectData->creationTimestamp,
                ]);
            }

            $project->znn_funds_needed = $projectData->znnFundsNeeded;
            $project->qsr_funds_needed = $projectData->qsrFundsNeeded;
            $project->znn_price = $znnPrice;
            $project->qsr_price = $qsrPrice;
            $project->vote_total = $projectData->votes->total;
            $project->vote_yes = $projectData->votes->yes;
            $project->vote_no = $projectData->votes->no;
            $project->modified_at = $projectData->lastUpdateTimestamp;
            $project->updated_at = $projectData->lastUpdateTimestamp;
            $project->created_at = $projectData->creationTimestamp;
            $project->save();

            return $project;
        }
    }

    private function notifyUsers($project)
    {
        $notificationType = NotificationType::findByCode('az-project-created');
        $subscribedUsers = User::whereHas('notification_types', function ($query) use ($notificationType) {
            return $query->where('code', $notificationType->code);
        })->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Accelerator\ProjectCreated($notificationType, $project)
        );
    }
}
