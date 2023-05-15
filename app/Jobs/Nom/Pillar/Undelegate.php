<?php

namespace App\Jobs\Nom\Pillar;

use App\Actions\SetBlockAsProcessed;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\PillarDelegator;
use App\Models\NotificationType;
use App\Models\User;
use Cache;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Notification;

class Undelegate implements ShouldQueue
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
        SyncPillars::dispatchSync();

        $delegation = $this->block
            ->account
            ->active_delegation;

        if ($delegation) {
            Cache::forget("pillar-{$delegation->pillar->id}-rank");

            $delegation->ended_at = $this->block->created_at;
            $delegation->save();

            $this->notifyUsers($delegation->pillar);
        }

        $delegators = PillarDelegator::isActive()->whereHas('account', fn ($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function notifyUsers($pillar)
    {
        $notificationType = NotificationType::findByCode('pillar-delegator-lost');
        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', $notificationType->code))
            ->whereHas('accounts', function ($query) use ($pillar) {
                $query->whereHas('pillars', fn ($query) => $query->where('id', $pillar->id));
            })
            ->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Pillar\LostDelegator($notificationType, $pillar, $this->block->account)
        );
    }
}
