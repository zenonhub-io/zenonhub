<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Pillar;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\PillarDelegator;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class Delegate implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public Pillar $pillar;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->processDelegate();
        //$this->notifyUsers();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processDelegate(): void
    {
        $blockData = $this->block->data->decoded;
        $this->pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $this->pillar) {
            exit;
        }

        SyncPillars::dispatchSync();

        Cache::forget("pillar-{$this->pillar->id}-rank");

        // Unset any previous delegation for the account -- TODO might not be needed?
        PillarDelegator::where('account_id', $this->block->account->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => $this->block->created_at,
            ]);

        PillarDelegator::create([
            'chain_id' => $this->block->chain->id,
            'pillar_id' => $this->pillar->id,
            'account_id' => $this->block->account->id,
            'started_at' => $this->block->created_at,
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
            new \App\Notifications\Nom\Pillar\NewDelegator($this->pillar, $this->block->account)
        );
    }
}
