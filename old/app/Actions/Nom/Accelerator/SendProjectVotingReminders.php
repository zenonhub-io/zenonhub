<?php

declare(strict_types=1);

namespace App\Actions\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Notifications\Nom\Accelerator\ProjectVoteReminder;
use Illuminate\Support\Facades\Notification;

class SendProjectVotingReminders
{
    public function execute()
    {
        $networkBot = new NetworkAlertBot;
        $projects = AcceleratorProject::whereNew()
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
