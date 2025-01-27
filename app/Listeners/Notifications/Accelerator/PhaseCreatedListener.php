<?php

declare(strict_types=1);

namespace App\Listeners\Notifications\Accelerator;

use App\Bots\NetworkAlertBot;
use App\Events\Indexer\Accelerator\PhaseCreated;
use App\Listeners\Notifications\BaseListener;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AccountBlock;
use App\Notifications\Nom\Accelerator\PhaseCreatedNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Lorisleiva\Actions\Concerns\AsAction;

class PhaseCreatedListener extends BaseListener
{
    use AsAction;

    private const string NOTIFICATION_TYPE = 'network-az';

    /**
     * Handle the event.
     */
    public function handle(AccountBlock $accountBlock, AcceleratorPhase $phase): void
    {
        $this->sendToUsers($phase);
        $this->sendToNetworkBot($phase);
        Artisan::call('sync:pillar-engagement-scores');
    }

    public function asListener(PhaseCreated $phaseCreatedEvent): void
    {
        $this->handle($phaseCreatedEvent->accountBlock, $phaseCreatedEvent->acceleratorPhase);
    }

    private function sendToUsers(AcceleratorPhase $phase): void
    {
        $this->getSubscribedUsers(self::NOTIFICATION_TYPE)
            ->chunkById(500, function ($users) use ($phase) {
                Notification::send($users, new PhaseCreatedNotification($phase));
            });
    }

    private function sendToNetworkBot(AcceleratorPhase $phase): void
    {
        Notification::send(new NetworkAlertBot, new PhaseCreatedNotification($phase));
    }
}
