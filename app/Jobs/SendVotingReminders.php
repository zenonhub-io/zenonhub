<?php

namespace App\Jobs;

use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
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

        $projects->each(function ($project) {
            $allVotes = $project->votes()->pluck('owner_id')->toArray();
            $missingVotes = Pillar::whereNotIn('owner_id', $allVotes)
                ->where('created_at', '<', $project->created_at)
                ->pluck('owner_id');

            $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-project-vote-reminder'))
                ->whereHas('nom_accounts', fn ($query) => $query->whereIn('account_id', $missingVotes))
                ->get();

            Notification::send(
                $subscribedUsers,
                new \App\Notifications\Nom\Accelerator\ProjectVoteReminder($project)
            );

            $project->send_reminders_at = null;
            $project->save();
        });
    }
}
