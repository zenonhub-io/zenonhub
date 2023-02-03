<?php

namespace App\Jobs\Accelerator;

use App;
use Str;
use Notification;
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

class PhaseAdded implements ShouldQueue
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
        $blockData = $this->block->data->decoded;
        $phaseData = $this->getPhaseData($blockData['id']);

        if (! $phaseData) {
            return;
        }

        $phase = $this->savePhase($phaseData);
        $this->notifyUsers($phase);
    }

    private function getPhaseData($projectId)
    {
        $znn = App::make('zenon.api');
        $projectData = $znn->accelerator->getProjectById($projectId)['data'];
        $project = AcceleratorProject::findByHash($projectId);
        $existingPhases = $project->phases->pluck('hash')->toArray();

        // Find phase project data
        foreach ($projectData->phases as $data) {
            if (! in_array($data->phase->id, $existingPhases)) {
                return $data;
            }
        }

        return null;
    }

    private function savePhase($phaseData)
    {
        $project = AcceleratorProject::findByHash($phaseData->phase->projectID);
        $znnPrice = App::make('coingeko.api')->historicPrice('zenon', 'usd', $phaseData->phase->creationTimestamp);

        if (! $znnPrice) {
            $this->release(5);
        } else {
            $qsrPrice = (float) $znnPrice / 10;
            $phase = $project?->phases()
                ->where('hash', $phaseData->phase->id)
                ->first();

            if (! $phase) {
                $phase = AcceleratorPhase::create([
                    'accelerator_project_id' => $project->id,
                    'hash' => $phaseData->phase->id,
                    'name' => $phaseData->phase->name,
                    'slug' => Str::slug($phaseData->phase->name),
                    'url' => $phaseData->phase->url,
                    'description' => $phaseData->phase->description,
                    'status' => $phaseData->phase->status,
                    'znn_funds_needed' => $phaseData->phase->znnFundsNeeded,
                    'qsr_funds_needed' => $phaseData->phase->qsrFundsNeeded,
                    'znn_price' => $znnPrice,
                    'qsr_price' => $qsrPrice,
                    'vote_total' => $phaseData->votes->total,
                    'vote_yes' => $phaseData->votes->yes,
                    'vote_no' => $phaseData->votes->no,
                    'send_reminders_at' => Carbon::parse($phaseData->phase->creationTimestamp)->addDays(13),
                    'accepted_at' => ($phaseData->phase->acceptedTimestamp ?: null),
                    'created_at' => $phaseData->phase->creationTimestamp,
                ]);
            }

            // Update existing details
            $phase->znn_funds_needed = $phaseData->znnFundsNeeded;
            $phase->qsr_funds_needed = $phaseData->qsrFundsNeeded;
            $phase->znn_price = $znnPrice;
            $phase->qsr_price = $qsrPrice;
            $phase->vote_total = $phaseData->votes->total;
            $phase->vote_yes = $phaseData->votes->yes;
            $phase->vote_no = $phaseData->votes->no;
            $phase->accepted_at = ($phaseData->phase->acceptedTimestamp ?: null);
            $phase->created_at = $phaseData->phase->creationTimestamp;
            $phase->updated_at = null;
            $phase->save();

            $project->modified_at = $phaseData->phase->creationTimestamp;
            $project->save();

            return $phase;
        }
    }

    private function notifyUsers($phase)
    {
        $notificationType = NotificationType::findByCode('az-phase-added');
        $subscribedUsers = User::whereHas('notification_types', function ($query) use ($notificationType) {
            return $query->where('code', $notificationType->code);
        })->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Accelerator\PhaseAdded($notificationType, $phase)
        );
    }
}
