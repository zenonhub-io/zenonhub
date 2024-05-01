<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\PillarDelegator;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class Undelegate extends AbstractIndexerAction
{
    public function handle(AccountBlock $accountBlock): void
    {
        SyncPillars::dispatchSync();

        $delegation = $this->block
            ->account
            ->active_delegation;

        if ($delegation) {
            Cache::forget("pillar-{$delegation->pillar->id}-rank");

            $delegation->ended_at = $this->accountBlock->created_at;
            $delegation->save();

            //$this->notifyUsers($delegation->pillar);
        }

        $delegators = PillarDelegator::isActive()->whereHas('account', fn ($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);

    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-delegator-lost'))
            ->whereHas('nom_accounts', function ($query) use ($pillar) {
                $query->whereHas('pillars', fn ($query) => $query->where('id', $pillar->id));
            })
            ->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Nom\Pillar\LostDelegator($pillar, $this->accountBlock->account)
        );
    }
}
