<?php

declare(strict_types=1);

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChangeAdministrator implements ShouldQueue
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
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $this->changeAdmin();

        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function changeAdmin(): void
    {
        $adminAddress = $this->block->data->decoded['administrator'];
        $oldAdmin = BridgeAdmin::getActiveAdmin();
        $newAdmin = BridgeAdmin::whereHas('account', fn ($q) => $q->where('address', $adminAddress))
            ->isProposed()
            ->sole();

        $this->revokeOldAndAcceptNewAdmin($oldAdmin, $newAdmin);
    }

    private function revokeOldAndAcceptNewAdmin(BridgeAdmin $oldAdmin, BridgeAdmin $newAdmin): void
    {
        $this->updateAdmin($oldAdmin, 'revoked_at');
        $this->updateAdmin($newAdmin, 'accepted_at');
    }

    private function updateAdmin(BridgeAdmin $admin, string $field): void
    {
        $admin->{$field} = $this->block->created_at;
        $admin->save();
    }
}
