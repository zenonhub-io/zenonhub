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
        $networkBot = new NetworkAlertBot();
        $phases = AcceleratorPhase::isOpen()
            ->shouldSendVotingReminder()
            ->get();

        $phases->each(function ($phase) use ($networkBot) {
            Notification::send(
                $networkBot,
                new PhaseVoteReminder($phase)
            );
        });
    }
}
