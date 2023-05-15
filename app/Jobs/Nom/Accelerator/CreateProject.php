<?php

namespace App\Jobs\Nom\Accelerator;

use App;
use App\Actions\SetBlockAsProcessed;
use App\Actions\UpdatePillarEngagementScores;
use App\Classes\Utilities;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use App\Models\NotificationType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Notification;
use Str;

class CreateProject implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public AcceleratorProject $project;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->saveProject();
        $this->notifyUsers();
        (new UpdatePillarEngagementScores())->execute();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function saveProject(): void
    {
        $znn = App::make('zenon.api');
        $projectData = $znn->accelerator->getProjectById($this->block->hash)['data'];

        $project = AcceleratorProject::where('hash', $projectData->id)->first();
        $znnPrice = App::make('coingeko.api')->historicPrice('zenon', 'usd', $projectData->creationTimestamp);
        $qsrPrice = App::make('coingeko.api')->historicPrice('quasar', 'usd', $projectData->creationTimestamp);

        // Projects created before QSR price available
        if (is_null($qsrPrice) && $znnPrice) {
            $qsrPrice = $znnPrice / 10;
        }

        if (! $project) {
            $owner = Utilities::loadAccount($projectData->owner);
            $project = AcceleratorProject::create([
                'chain_id' => $this->block->chain->id,
                'owner_id' => $owner->id,
                'hash' => $projectData->id,
                'name' => $projectData->name,
                'slug' => Str::slug($projectData->name),
                'url' => $projectData->url,
                'description' => $projectData->description,
                'status' => $projectData->status,
                'znn_requested' => $projectData->znnFundsNeeded,
                'qsr_requested' => $projectData->qsrFundsNeeded,
                'znn_price' => $znnPrice ?: 0,
                'qsr_price' => $qsrPrice ?: 0,
                'vote_total' => $projectData->votes->total,
                'vote_yes' => $projectData->votes->yes,
                'vote_no' => $projectData->votes->no,
                'send_reminders_at' => Carbon::parse($projectData->creationTimestamp)->addDays(13),
                'modified_at' => $projectData->creationTimestamp,
                'updated_at' => $projectData->lastUpdateTimestamp,
                'created_at' => $projectData->creationTimestamp,
            ]);
        }

        $project->znn_requested = $projectData->znnFundsNeeded;
        $project->qsr_requested = $projectData->qsrFundsNeeded;
        $project->znn_price = $znnPrice ?: 0;
        $project->qsr_price = $qsrPrice ?: 0;
        $project->vote_total = $projectData->votes->total;
        $project->vote_yes = $projectData->votes->yes;
        $project->vote_no = $projectData->votes->no;
        $project->modified_at = $projectData->lastUpdateTimestamp;
        $project->updated_at = $projectData->lastUpdateTimestamp;
        $project->created_at = $projectData->creationTimestamp;
        $project->save();

        $this->project = $project;
    }

    private function notifyUsers(): void
    {
        $notificationType = NotificationType::findByCode('az-project-created');
        $subscribedUsers = User::whereHas('notification_types', function ($query) use ($notificationType) {
            return $query->where('code', $notificationType->code);
        })->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Accelerator\ProjectCreated($notificationType, $this->project)
        );
    }
}
