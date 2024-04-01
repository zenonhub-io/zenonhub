<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Pillar;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
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
        $blockData = $this->block->data->decoded;
        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            return;
        }

        $pillar->weight = 0;
        $pillar->produced_momentums = 0;
        $pillar->expected_momentums = 0;
        $pillar->missed_momentums = 0;
        $pillar->revoked_at = $this->block->momentum->created_at;
        $pillar->save();

        $this->notifyUsers($pillar);
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function notifyUsers($pillar): void
    {
        $subscribedUsers = NotificationType::getSubscribedUsers('network-pillar');
        $networkBot = new \App\Bots\NetworkAlertBot;

        Notification::send(
            $subscribedUsers->prepend($networkBot),
            new \App\Notifications\Nom\Pillar\Revoked($pillar)
        );
    }
}
