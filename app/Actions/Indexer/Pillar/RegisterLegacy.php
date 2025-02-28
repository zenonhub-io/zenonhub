<?php

declare(strict_types=1);

namespace App\Actions\Indexer\Pillar;

use App\Actions\Indexer\AbstractContractMethodProcessor;
use App\Events\Indexer\Pillar\PillarRegistered;
use App\Exceptions\IndexerActionValidationException;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterLegacy extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $accountBlock->load('token');
        $blockData = $accountBlock->data->decoded;

        try {
            $this->validateAction($accountBlock);
        } catch (IndexerActionValidationException $e) {
            Log::error('Contract Method Processor - Pillar: RegisterLegacy failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        $pillar = Pillar::updateOrCreate([
            'chain_id' => $accountBlock->chain_id,
            'owner_id' => $accountBlock->account_id,
            'name' => $blockData['name'],
            'slug' => Str::slug($blockData['name']),
        ], [
            'producer_account_id' => load_account($blockData['producerAddress'])->id,
            'withdraw_account_id' => load_account(($blockData['withdrawAddress'] ?? $blockData['rewardAddress']))->id,
            'qsr_burn' => 150000 * config('nom.decimals'),
            'momentum_rewards' => $blockData['giveBlockRewardPercentage'],
            'delegate_rewards' => $blockData['giveDelegateRewardPercentage'],
            'is_legacy' => true,
            'created_at' => $accountBlock->created_at,
        ]);

        PillarRegistered::dispatch($accountBlock, $pillar);

        Log::info('Contract Method Processor - Pillar: RegisterLegacy complete', [
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
         */
        [$accountBlock] = func_get_args();
        $blockData = $accountBlock->data->decoded;

        if ($blockData['name'] === '' || strlen($blockData['name']) > config('nom.pillar.nameLengthMax')) {
            throw new IndexerActionValidationException('Pillar name is too long');
        }

        if (! preg_match('/^([a-zA-Z0-9]+[-._]?)*[a-zA-Z0-9]$/', $blockData['name'])) {
            throw new IndexerActionValidationException('Pillar name is invalid');
        }

        if ($blockData['giveBlockRewardPercentage'] > 100 || $blockData['giveBlockRewardPercentage'] < 0) {
            throw new IndexerActionValidationException('Block reward percentage is invalid');
        }

        if ($blockData['giveDelegateRewardPercentage'] > 100 || $blockData['giveDelegateRewardPercentage'] < 0) {
            throw new IndexerActionValidationException('Delegate reward percentage is invalid');
        }

        if ($accountBlock->token->token_standard !== app('znnToken')->token_standard) {
            throw new IndexerActionValidationException('Token must be ZNN');
        }

        if ($accountBlock->amount !== config('nom.pillar.znnStakeAmount')) {
            throw new IndexerActionValidationException('Amount doesnt match pillar registration cost');
        }
    }
}
