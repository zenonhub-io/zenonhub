<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class Delegate extends AbstractContractMethodProcessor
{
    public Pillar $pillar;

    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            return;
        }

        Cache::forget("{$pillar->cacheKey()}|pillar-rank");

        $accountBlock->account
            ->delegations()
            ->newPivotStatementForId($accountBlock->account->id)
            ->where('ended_at', null)
            ->update(['ended_at' => $accountBlock->created_at]);

        $accountBlock->account->delegations()->attach($pillar->id, [
            'started_at' => $accountBlock->created_at,
        ]);
    }

    //    private function notifyUsers(): void
    //    {
    //        $subscribedUsers = User::whereHas('notification_types', fn ($query) => $query->where('code', 'pillar-delegator-added'))
    //            ->whereHas('nom_accounts', function ($query) {
    //                $query->whereHas('pillars', fn ($query) => $query->where('id', $this->pillar->id));
    //            })
    //            ->get();
    //
    //        Notification::send(
    //            $subscribedUsers,
    //            new \App\Notifications\Nom\Pillar\NewDelegator($this->pillar, $accountBlock->account)
    //        );
    //    }
}
