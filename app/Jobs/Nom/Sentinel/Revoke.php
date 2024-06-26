<?php

namespace App\Jobs\Nom\Sentinel;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Sentinel;
use App\Models\NotificationType;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class Revoke implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $sentinel = Sentinel::where('owner_id', $this->block->account->id)->isActive()->first();

        if (! $sentinel) {
            return;
        }

        $sentinel->revoked_at = $this->block->created_at;
        $sentinel->save();

        $this->notifyUsers($sentinel);
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function notifyUsers($sentinel): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-sentinel');
        $networkBot = new \App\Bots\NetworkAlertBot();

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Sentinel\Revoked($sentinel)
        );
    }
}
