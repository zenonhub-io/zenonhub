<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Bridge;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Indexer\Events\Bridge\GuardiansNominated;
use App\Domains\Indexer\Exceptions\IndexerActionValidationException;
use App\Domains\Nom\Actions\CheckTimeChallenge;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\BridgeAdmin;
use App\Domains\Nom\Models\BridgeGuardian;
use Illuminate\Support\Facades\Log;

class NominateGuardians extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::info('Contract Method Processor - Bridge: NominateGuardians failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $nominatedGuardians = BridgeGuardian::setNewGuardians($blockData['guardians'], $accountBlock->created_at);

        GuardiansNominated::dispatch($accountBlock, $nominatedGuardians);

        Log::info('Contract Method Processor - Bridge: NominateGuardians complete', [
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

        if (! isset($blockData['guardians']) || ! is_array($blockData['guardians'])) {
            throw new IndexerActionValidationException('Guardians array not set');
        }

        if (count($blockData['guardians']) < config('nom.bridge.minGuardians')) {
            throw new IndexerActionValidationException('Not enough guardians nominated');
        }

        $challengeHashData = json_encode($blockData['guardians']);
        $timeChallenge = (new CheckTimeChallenge)
            ->handle($accountBlock, $challengeHashData, config('nom.bridge.minSoftDelay'));

        if ($timeChallenge->is_active) {
            throw new IndexerActionValidationException('Time challenge is still active');
        }
    }
}
