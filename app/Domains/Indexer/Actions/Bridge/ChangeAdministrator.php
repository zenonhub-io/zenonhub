<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\AdministratorChanged;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Actions\CheckTimeChallenge;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use Illuminate\Support\Facades\Log;

class ChangeAdministrator extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: ChangeAdministrator failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $adminAccount = load_account($accountBlock['address']);
        $newAdmin = BridgeAdmin::setNewAdmin($adminAccount, $accountBlock->created_at);

        AdministratorChanged::dispatch($accountBlock, $newAdmin);

        Log::info('Contract Method Processor - Bridge: ChangeAdministrator complete', [
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

        if (! $bridgeAdmin->account_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Action sent from non admin');
        }

        $challengeHashData = $blockData['address'];
        $timeChallenge = (new CheckTimeChallenge)
            ->handle($accountBlock, $challengeHashData, config('nom.bridge.minAdministratorDelay'));

        if ($timeChallenge->is_active) {
            throw new IndexerActionValidationException('Time challenge is still active');
        }
    }
}
