<?php

namespace App\Jobs\Pillars;

use Cache;
use Notification;
use App\Jobs\Sync\Pillars as SyncPillars;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use App\Models\Nom\PillarDelegator;
use App\Models\NotificationType;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class Delegate implements ShouldQueue
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
        $blockData = $this->block->data->decoded;
        $pillar = Pillar::where('name', $blockData['name'])->first();

        if( app()->environment() !== 'local') {
            SyncPillars::dispatchSync();
        }

        Cache::forget("pillar-{$pillar->id}-rank");

        // Unset any previous delegation for the account -- TODO might not be needed?
        PillarDelegator::where('account_id', $this->block->account->id)
            ->whereNull('ended_at')
            ->update([
                'ended_at' => $this->block->created_at,
            ]);

        if ($pillar) {
            PillarDelegator::create([
                'pillar_id' => $pillar->id,
                'account_id' => $this->block->account->id,
                'started_at' => $this->block->created_at,
            ]);
        }

        $delegators = PillarDelegator::isActive()->whereHas('account', fn($query) => $query->where('znn_balance', '>', '0'))->count();
        Cache::put('delegators-count', $delegators);

        $this->notifyUsers($pillar);
    }

    private function notifyUsers($pillar)
    {
        $notificationType = NotificationType::findByCode('pillar-delegator-added');
        $subscribedUsers = User::whereHas('notification_types', fn($query) => $query->where('code', $notificationType->code))
            ->whereHas('accounts', function ($query) use ($pillar)  {
                $query->whereHas('pillars', fn($query) => $query->where('id', $pillar->id));
            })
            ->get();

        Notification::send(
            $subscribedUsers,
            new \App\Notifications\Pillar\NewDelegator($notificationType, $pillar, $this->block->account)
        );
    }
}
