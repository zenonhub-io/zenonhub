<?php

namespace App\Notifications\Nom\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Models\Nom\AcceleratorProject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class ProjectVoteReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected AcceleratorProject $project;

    public function __construct(AcceleratorProject $project)
    {
        $this->project = $project;
    }

    public function via($notifiable): array
    {
        $channels = [];

        if ($notifiable instanceof NetworkAlertBot) {
            if (config('network-alerts.twitter.enabled')) {
                $channels[] = TwitterChannel::class;
            }
        }

        if ($notifiable instanceof User) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $accountIds = $notifiable->nom_accounts->pluck('id')->toArray();
        $allVotes = $this->project->votes()->pluck('owner_id')->toArray();
        $missingVotes = \App\Models\Nom\Pillar::whereNotIn('owner_id', $allVotes)
            ->where('created_at', '<', $this->project->created_at)
            ->get();

        $pillars = $missingVotes->map(function ($pillar) use ($accountIds) {
            if (in_array($pillar->owner_id, $accountIds)) {
                return $pillar;
            }

            return null;
        })->filter();

        return (new MailMessage)
            ->subject(get_env_prefix().'Voting reminders')
            ->markdown('mail.notifications.az.project-vote-reminder', [
                'user' => $notifiable,
                'project' => $this->project,
                'pillars' => $pillars,
            ]);
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        return new TwitterStatusUpdate('Testing Twitter network alerts');
    }
}
