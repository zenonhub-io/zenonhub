<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Bridge;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Actions\Nom\CheckTimeChallenge;
use App\Events\Indexer\Bridge\AdministratorChanged;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\BridgeAdmin;
use Illuminate\Support\Facades\Log;

class ChangeAdministrator extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Bridge: ChangeAdministrator failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $adminAccount = load_account($blockData['administrator']);
        $newAdmin = BridgeAdmin::setNewAdmin($adminAccount, $accountBlock->created_at);

        AdministratorChanged::dispatch($accountBlock, $newAdmin);

        Log::error('Contract Method Processor - Bridge: ChangeAdministrator complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    /**
     * @throws IndexerActionValidationException
     */
    public function validateAction(): void
    {
        /**
         * @var AccountBlock $accountBlock
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        $bridgeAdmin = BridgeAdmin::getActiveAdmin();

        if ($bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        $challengeHashData = $blockData['administrator'];
        $timeChallenge = CheckTimeChallenge::run($accountBlock, $challengeHashData, config('nom.bridge.minAdministratorDelay'));

        if ($timeChallenge->is_active) {
            throw new IndexerActionValidationException('Time challenge is still active');
        }
    }
}
