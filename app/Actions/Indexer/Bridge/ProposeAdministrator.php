<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Bridge\AdministratorProposed;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use App\Models\Nom\BridgeGuardian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProposeAdministrator extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: ProposeAdministrator failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $proposedAccount = load_account($blockData['address']);
        $proposedAdmin = BridgeAdmin::create([
            'account_id' => $proposedAccount->id,
            'nominated_by_id' => $accountBlock->account_id,
            'nominated_at' => $accountBlock->created_at,
        ]);

        $this->checkVotes($accountBlock);

        AdministratorProposed::dispatch($accountBlock, $proposedAdmin);

        Log::info('Contract Method Processor - Bridge: ProposeAdministrator complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        $isGuardian = BridgeGuardian::whereActive()->where('account_id', $accountBlock->account_id)->exists();

        if (! $isGuardian) {
            throw new IndexerActionValidationException('Action sent from non guardian');
        }
    }

    private function checkVotes(AccountBlock $accountBlock): void
    {
        // if over half the guardians vote for the same address it becomes the new admin
        $numGuardians = BridgeGuardian::whereActive()->count();
        $guardianVotesNeeded = $numGuardians / 2;
        $nominations = BridgeAdmin::select(['*', DB::raw('count(*) as count')])
            ->whereNull('accepted_at')
            ->groupBy('account_id')
            ->get();

        $newAdmin = $nominations->firstWhere(function ($nomination) use ($guardianVotesNeeded) {
            if ($nomination->count < $guardianVotesNeeded) {
                return null;
            }

            return $nomination;
        });

        if ($newAdmin) {
            BridgeAdmin::setNewAdmin($newAdmin->account, $accountBlock->created_at);
        }
    }
}
