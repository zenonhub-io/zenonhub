<?php

namespace App\Jobs\Nom\Accelerator;

use App;
use App\Actions\SetBlockAsProcessed;
use App\Actions\UpdatePillarEngagementScores;
use App\Models\Nom\AcceleratorPhase;
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

class AddPhase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public AcceleratorPhase $phase;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->savePhase();
        $this->notifyUsers();
        (new UpdatePillarEngagementScores())->execute();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function savePhase(): void
    {
        $znn = App::make('zenon.api');
        $phaseData = $znn->accelerator->getPhaseById($this->block->hash)['data'];

        $project = AcceleratorProject::findByHash($phaseData->phase->projectID);
        $znnPrice = App::make('coingeko.api')->historicPrice('zenon', 'usd', $phaseData->phase->creationTimestamp);
        $qsrPrice = App::make('coingeko.api')->historicPrice('quasar', 'usd', $phaseData->phase->creationTimestamp);

        // Projects created before QSR price available
        if (is_null($qsrPrice) && $znnPrice) {
            $qsrPrice = $znnPrice / 10;
        }

        $phase = $project?->phases()
            ->where('hash', $phaseData->phase->id)
            ->first();

        if (! $phase) {
            $phase = AcceleratorPhase::create([
                'chain_id' => $this->block->chain->id,
                'accelerator_project_id' => $project->id,
                'hash' => $phaseData->phase->id,
                'name' => $phaseData->phase->name,
                'slug' => Str::slug($phaseData->phase->name),
                'url' => $phaseData->phase->url,
                'description' => $phaseData->phase->description,
                'status' => $phaseData->phase->status,
                'znn_requested' => $phaseData->phase->znnFundsNeeded,
                'qsr_requested' => $phaseData->phase->qsrFundsNeeded,
                'znn_price' => $znnPrice ?: 0,
                'qsr_price' => $qsrPrice ?: 0,
                'vote_total' => $phaseData->votes->total,
                'vote_yes' => $phaseData->votes->yes,
                'vote_no' => $phaseData->votes->no,
                'send_reminders_at' => Carbon::parse($phaseData->phase->creationTimestamp)->addDays(13),
                'accepted_at' => ($phaseData->phase->acceptedTimestamp ?: null),
                'created_at' => $phaseData->phase->creationTimestamp,
            ]);
        }

        // Update existing details
        $phase->znn_requested = $phaseData->phase->znnFundsNeeded;
        $phase->qsr_requested = $phaseData->phase->qsrFundsNeeded;
        $phase->znn_price = $znnPrice ?: 0;
        $phase->qsr_price = $qsrPrice ?: 0;
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
        $notificationType = NotificationType::findByCode('az-phase-added');
        $subscribedUsers = User::whereHas('notification_types', function ($query) use ($notificationType) {
            return $query->where('code', $notificationType->code);
        })->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Accelerator\PhaseAdded($notificationType, $this->phase)
        );
    }
}
