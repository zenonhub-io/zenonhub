<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Accelerator\ProjectCreated;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AccountBlock;
use App\Notifications\Nom\Accelerator\ProjectCreatedNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class ProjectCreatedListener extends BaseListener
{
    use AsAction;

    private const string NOTIFICATION_TYPE = 'network-az';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, AcceleratorProject $project): void
    {
        $this->sendToUsers($project);
        $this->sendToNetworkBot($project);
        Artisan::call('sync:pillar-engagement-scores');
    }

    public function asListener(ProjectCreated $projectCreatedEvent): void
    {
        $this->handle($projectCreatedEvent->accountBlock, $projectCreatedEvent->acceleratorProject);
    }

    private function sendToUsers(AcceleratorProject $project): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($project) {
                Notification::send($users, new ProjectCreatedNotification($project));
            });
    }

    private function sendToNetworkBot(AcceleratorProject $phase): void
    {
        Notification::send(new NetworkAlertBot, new ProjectCreatedNotification($phase));
    }
}
