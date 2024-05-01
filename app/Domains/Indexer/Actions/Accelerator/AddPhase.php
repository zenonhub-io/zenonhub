<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Actions\UpdatePillarEngagementScores;
use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AccountBlock;
use App\Models\NotificationType;
use App\Services\CoinGecko;
use App\Services\ZenonSdk;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AddPhase extends AbstractIndexerAction
{
    public AcceleratorPhase $phase;

    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;

        $this->savePhase();
        $this->notifyUsers();
        (new UpdatePillarEngagementScores)->execute();
    }

    private function savePhase(): void
    {
        $znn = App::make(ZenonSdk::class);
        $phaseData = $znn->accelerator->getPhaseById($this->accountBlock->hash)['data'];

        $project = AcceleratorProject::findBy('hash', $phaseData->phase->projectID);
        $znnPrice = App::make(CoinGecko::class)->historicPrice('zenon-2', 'usd', $phaseData->phase->creationTimestamp);
        $qsrPrice = App::make(CoinGecko::class)->historicPrice('quasar', 'usd', $phaseData->phase->creationTimestamp);

        // Projects created before QSR price available
        if (is_null($qsrPrice) && $znnPrice) {
            $qsrPrice = $znnPrice / 10;
        }

        $phase = $project?->phases()
            ->where('hash', $phaseData->phase->id)
            ->first();

        if (! $phase) {
            $phase = AcceleratorPhase::create([
                'chain_id' => $this->accountBlock->chain->id,
                'project_id' => $project->id,
                'hash' => $phaseData->phase->id,
                'name' => $phaseData->phase->name,
                'slug' => Str::slug($phaseData->phase->name),
                'url' => $phaseData->phase->url,
                'description' => $phaseData->phase->description,
                'status' => $phaseData->phase->status,
                'znn_requested' => $phaseData->phase->znnFundsNeeded,
                'qsr_requested' => $phaseData->phase->qsrFundsNeeded,
                'znn_price' => $znnPrice ?: null,
                'qsr_price' => $qsrPrice ?: null,
                'vote_total' => $phaseData->votes->total,
                'vote_yes' => $phaseData->votes->yes,
                'vote_no' => $phaseData->votes->no,
                'accepted_at' => ($phaseData->phase->acceptedTimestamp ?: null),
                'created_at' => $phaseData->phase->creationTimestamp,
            ]);
        }

        // Update existing details
        $phase->znn_requested = $phaseData->phase->znnFundsNeeded;
        $phase->qsr_requested = $phaseData->phase->qsrFundsNeeded;
        $phase->znn_price = $znnPrice ?: null;
        $phase->qsr_price = $qsrPrice ?: null;
        $phase->vote_total = $phaseData->votes->total;
        $phase->vote_yes = $phaseData->votes->yes;
        $phase->vote_no = $phaseData->votes->no;
        $phase->accepted_at = ($phaseData->phase->acceptedTimestamp ?: null);
        $phase->created_at = $phaseData->phase->creationTimestamp;
        $phase->updated_at = null;
        $phase->save();

        $project->modified_at = $phaseData->phase->creationTimestamp;
        $project->save();

        $this->phase = $phase;
    }

    private function notifyUsers(): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-az');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Accelerator\PhaseAdded($this->phase)
        );
    }
}
