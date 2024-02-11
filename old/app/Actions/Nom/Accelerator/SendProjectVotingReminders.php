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
        $networkBot = new NetworkAlertBot();
        $projects = AcceleratorProject::isNew()
            ->shouldSendVotingReminder()
            ->get();

        $projects->each(function ($project) use ($networkBot) {
            Notification::send(
                $networkBot,
                new ProjectVoteReminder($project)
            );
        });
    }
}
