<?php

namespace App\Actions\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\AcceleratorProject;
use App\Notifications\Nom\Accelerator\ProjectVoteReminder;
use Illuminate\Support\Facades\Notification;

class SendProjectVotingReminders
{
    public function execute()
    {
        $projects = AcceleratorProject::isNew()->get();
        $networkBot = new NetworkAlertBot();

        $projects->each(function ($project, int $index) use ($networkBot) {
            Notification::send(
                $networkBot,
                (new ProjectVoteReminder($project))->delay(now()->addMinutes($index))
            );
        });
    }
}
