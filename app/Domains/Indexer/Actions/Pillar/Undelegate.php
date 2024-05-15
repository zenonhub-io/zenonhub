<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Pillar;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Pillar\AccountUndelegated;
use App\Domains\Nom\Models\AccountBlock;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;

class Undelegate extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $this->accountBlock = $accountBlock;
        $blockData = $accountBlock->data->decoded;

        $delegation = $accountBlock->account
            ->delegations()
            ->wherePivotNull('ended_at')
            ->first();

        if (! $delegation) {
            return;
        }

        Cache::forget("{$delegation->cacheKey()}|pillar-rank");

        $accountBlock->account
            ->delegations()
            ->updateExistingPivot($delegation->id, [
                'ended_at' => $accountBlock->created_at,
            ]);

        AccountUndelegated::dispatch($accountBlock, $accountBlock->account, $delegation);
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
            new \App\Notifications\Nom\Pillar\LostDelegator($pillar, $accountBlock->account)
        );
    }
}
