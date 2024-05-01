<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractIndexerAction;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\PillarDelegator;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class Delegate extends AbstractIndexerAction
{
    public Pillar $pillar;

    public function handle(AccountBlock $accountBlock): void
    {
        $this->processDelegate();
        //$this->notifyUsers();

    }

    private function processDelegate(): void
    {
        $blockData = $this->accountBlock->data->decoded;
        $this->pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $this->pillar) {
            exit;
        }

        SyncPillars::dispatchSync();

        Cache::forget("pillar-{$this->pillar->id}-rank");

        // Unset any previous delegation for the account -- TODO might not be needed?
        PillarDelegator::where('account_id', $this->accountBlock->account->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => $this->accountBlock->created_at,
            ]);

        PillarDelegator::create([
            'chain_id' => $this->accountBlock->chain->id,
            'pillar_id' => $this->pillar->id,
            'account_id' => $this->accountBlock->account->id,
            'started_at' => $this->accountBlock->created_at,
        ]);

        $delegators = PillarDelegator::isActive()->whereHas('account', fn ($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);
    }

    private function notifyUsers(): void
    {
        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-delegator-added'))
            ->whereHas('nom_accounts', function ($query) {
                $query->whereHas('pillars', fn ($query) => $query->where('id', $this->pillar->id));
            })
            ->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Nom\Pillar\NewDelegator($this->pillar, $this->accountBlock->account)
        );
    }
}
