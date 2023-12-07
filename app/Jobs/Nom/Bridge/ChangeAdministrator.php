<?php

namespace App\Jobs\Nom\Bridge;

use App\Actions\SetBlockAsProcessed;
use App\Classes\Utilities;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
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
        if (! Utilities::validateBridgeTx($this->block)) {
            Log::error('Bridge action sent from non-admin');

            return;
        }

        $adminAddress = $this->block->data->decoded['administrator'];
        $oldAdmin = BridgeAdmin::getActiveAdmin();
        $newAdmin = BridgeAdmin::whereHas('account', fn ($q) => $q->where('address', $adminAddress))
            ->isProposed()
            ->sole();

        $this->revokeOldAndAcceptNewAdmin($oldAdmin, $newAdmin);

        (new SetBlockAsProcessed($this->block))->execute();
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
