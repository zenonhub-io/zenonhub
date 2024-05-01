<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Actions\UpdatePillarEngagementScores;
use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Models\NotificationType;
use App\Services\CoinGecko;
use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class CreateProject extends AbstractIndexerAction
{
    public AcceleratorProject $project;

    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;

        $this->saveProject();
        $this->notifyUsers();
        (new UpdatePillarEngagementScores)->execute();
    }

    private function saveProject(): void
    {
        $znn = App::make(ZenonSdk::class);
        $projectData = $znn->accelerator->getProjectById($this->accountBlock->hash)['data'];

        $project = AcceleratorProject::where('hash', $projectData->id)->first();
        $znnPrice = App::make(CoinGecko::class)->historicPrice('zenon-2', 'usd', $projectData->creationTimestamp);
        $qsrPrice = App::make(CoinGecko::class)->historicPrice('quasar', 'usd', $projectData->creationTimestamp);

        // Projects created before QSR price available
        if (is_null($qsrPrice) && $znnPrice) {
            $qsrPrice = $znnPrice / 10;
        }

        if (! $project) {
            $owner = load_account($projectData->owner);
            $project = AcceleratorProject::create([
                'chain_id' => $this->accountBlock->chain->id,
                'owner_id' => $owner->id,
                'hash' => $projectData->id,
                'name' => $projectData->name,
                'slug' => Str::slug($projectData->name),
                'url' => $projectData->url,
                'description' => $projectData->description,
                'status' => $projectData->status,
                'znn_requested' => $projectData->znnFundsNeeded,
                'qsr_requested' => $projectData->qsrFundsNeeded,
                'znn_price' => $znnPrice ?: null,
                'qsr_price' => $qsrPrice ?: null,
                'vote_total' => $projectData->votes->total,
                'vote_yes' => $projectData->votes->yes,
                'vote_no' => $projectData->votes->no,
                'modified_at' => $projectData->creationTimestamp,
                'updated_at' => $projectData->lastUpdateTimestamp,
                'created_at' => $projectData->creationTimestamp,
            ]);
        }

        $project->znn_requested = $projectData->znnFundsNeeded;
        $project->qsr_requested = $projectData->qsrFundsNeeded;
        $project->znn_price = $znnPrice ?: null;
        $project->qsr_price = $qsrPrice ?: null;
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
        $subscribedUsers = NotificationType::getSubscribedUsers('network-az');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Accelerator\ProjectCreated($this->project)
        );
    }
}
