<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\PillarUpdated;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Illuminate\Support\Facades\Log;

class UpdatePillar extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::firstWhere('name', $blockData['name']);

        try {
            $this->validateAction($accountBlock, $pillar);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Pillar: UpdatePillar failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $rewardsChanged = false;
        if (
            $pillar->momentum_rewards !== (int) $blockData['giveBlockRewardPercentage'] ||
            $pillar->delegate_rewards !== (int) $blockData['giveDelegateRewardPercentage']
        ) {
            $rewardsChanged = true;
        }

        $producerAddress = load_account($blockData['producerAddress']);
        $rewardAddress = load_account($blockData['rewardAddress']);

        $pillar->producer_account_id = $producerAddress->id;
        $pillar->withdraw_account_id = $rewardAddress->id;
        $pillar->momentum_rewards = $blockData['giveBlockRewardPercentage'];
        $pillar->delegate_rewards = $blockData['giveDelegateRewardPercentage'];
        $pillar->updated_at = $accountBlock->created_at;
        $pillar->save();

        $pillar->updateHistory()->create([
            'producer_account_id' => $producerAddress->id,
            'withdraw_account_id' => $rewardAddress->id,
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_reward_change' => $rewardsChanged,
            'updated_at' => $accountBlock->created_at,
        ]);

        PillarUpdated::dispatch($accountBlock, $pillar);

        Log::info('Contract Method Processor - Pillar: UpdatePillar complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
            'pillar' => $pillar,
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
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if (! $pillar) {
            throw new IndexerActionValidationException('Invalid pillar');
        }

        if ($pillar->owner_id !== $accountBlock->account_id) {
            throw new IndexerActionValidationException('Account is not pillar owner');
        }

        if ($pillar->revoked_at !== null) {
            throw new IndexerActionValidationException('Pillar is revoked');
        }

        if ($blockData['giveBlockRewardPercentage'] > 100 || $blockData['giveBlockRewardPercentage'] < 0) {
            throw new IndexerActionValidationException('Invalid block reward percentage');
        }

        if ($blockData['giveDelegateRewardPercentage'] > 100 || $blockData['giveDelegateRewardPercentage'] < 0) {
            throw new IndexerActionValidationException('Invalid delegate reward percentage');
        }
    }
}
