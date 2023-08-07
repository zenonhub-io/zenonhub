<?php

namespace App\Jobs;

use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendVotingReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public function handle(): void
    {
        $projects = AcceleratorProject::reminderDue()->get();
        $notificationType = NotificationType::findByCode('pillar-project-vote-reminder');

        $projects->each(function ($project) use ($notificationType) {
            $allVotes = $project->votes()->pluck('owner_id')->toArray();
            $missingVotes = Pillar::whereNotIn('owner_id', $allVotes)
                ->where('created_at', '<', $project->created_at)
                ->pluck('owner_id');

            $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', $notificationType->code))
                ->whereHas('nom_accounts', fn ($query) => $query->whereIn('account_id', $missingVotes))
                ->get();

            Notification::send(
                $subscribedUsers,
                new \App\Notifications\Nom\Accelerator\ProjectVoteReminder($notificationType, $project)
            );

            $project->send_reminders_at = null;
            $project->save();
        });
    }
}
