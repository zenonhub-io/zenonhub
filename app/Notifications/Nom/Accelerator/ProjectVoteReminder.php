<?php

namespace App\Notifications\Nom\Accelerator;

use App\Models\Nom\AcceleratorProject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterMessage;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class ProjectVoteReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected AcceleratorProject $project)
    {
    }

    public function via($notifiable): array
    {
        $channels = [];

        if (config('bots.network-alerts.twitter.enabled')) {
            $channels[] = TwitterChannel::class;
        }

        return $channels;
    }

    public function toTwitter($notifiable): TwitterMessage
    {
        $totalYes = $this->project->votes()->where('is_yes', '1')->count();
        $totalNo = $this->project->votes()->where('is_no', '1')->count();
        $totalAbstain = $this->project->votes()->where('is_abstain', '1')->count();
        $totalVotesNeeded = $this->project->total_more_votes_needed;
        $votesText = Str::plural('vote', $totalVotesNeeded);
        $progressBar = $this->generateProgressBar($this->project->votes_percentage);

        $link = route('az.project', [
            'hash' => $this->project->hash,
            'utm_source' => 'network_bot',
            'utm_medium' => 'twitter',
        ]);

        return new TwitterStatusUpdate("🗳 {$this->project->name} needs {$totalVotesNeeded} more {$votesText}!

✅ $totalYes | ❌ $totalNo | ⚫️ $totalAbstain

$progressBar

🔗 $link

#Zenon #AcceleratorZ");
    }

    private function generateProgressBar(int $percentage): string
    {
        $empty = '□';
        $full = '■';
        $barTotalLength = 10;

        $fullBars = round($percentage / 10);
        $emptyBars = $barTotalLength - $fullBars;

        return str_repeat($full, $fullBars).str_repeat($empty, $emptyBars)." {$percentage}%";
    }
}
