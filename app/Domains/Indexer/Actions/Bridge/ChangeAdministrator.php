<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use Illuminate\Support\Facades\Log;

class ChangeAdministrator extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        if (! validate_bridge_tx($this->block)) {
            Log::warning('Bridge action sent from non-admin');

            return;
        }

        $this->changeAdmin();

    }

    private function changeAdmin(): void
    {
        $adminAddress = $accountBlock->data->decoded['administrator'];
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
        $admin->{$field} = $accountBlock->created_at;
        $admin->save();
    }
}
