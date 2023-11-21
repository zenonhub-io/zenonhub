<?php

namespace App\Actions\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\AcceleratorPhase;
use App\Notifications\Nom\Accelerator\PhaseVoteReminder;
use Illuminate\Support\Facades\Notification;

class SendPhaseVotingReminders
{
    public function execute()
    {
        $cutoff = now()->subMonths(3);
        $phases = AcceleratorPhase::isOpen()->createdAfter($cutoff)->get();
        $networkBot = new NetworkAlertBot();

        $phases->each(function ($phase, int $index) use ($networkBot) {
            Notification::send(
                $networkBot,
                (new PhaseVoteReminder($phase))->delay(now()->addMinutes($index))
            );
        });
    }
}
