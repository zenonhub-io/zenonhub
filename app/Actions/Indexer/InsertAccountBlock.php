<?php

declare(strict_types=1);

namespace App\Actions\Indexer;

use App\DataTransferObjects\Nom\AccountBlockDTO;
use App\Enums\Nom\AccountBlockTypesEnum;
use App\Events\Indexer\AccountBlockInserted;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\ContractMethod;
use App\Models\Nom\Momentum;
use App\Services\ZenonSdk\ZenonSdk;
use DigitalSloth\ZnnPhp\Exceptions\DecodeException;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InsertAccountBlock
{
    /**
     * @throws DecodeException
     */
    public function execute(AccountBlockDTO $accountBlockDTO): void
    {
        $block = AccountBlock::firstWhere('hash', $accountBlockDTO->hash);
        if ($block) {
            return;
        }

        Log::debug('Insert Account Block', [
            'hash' => $accountBlockDTO->hash,
        ]);

        $chain = app('currentChain');
        $account = load_account($accountBlockDTO->address);
        $toAccount = load_account($accountBlockDTO->toAddress);
        $token = load_token($accountBlockDTO->token?->tokenStandard);
        $momentum = Momentum::firstWhere('hash', $accountBlockDTO->confirmationDetail->momentumHash);
        $momentumAcknowledged = Momentum::firstWhere('hash', $accountBlockDTO->momentumAcknowledged->hash);

        if (! $account->public_key) {
            $account->public_key = $accountBlockDTO->publicKey;
        }

        if (! $account->first_active_at) {
            $account->first_active_at = $accountBlockDTO->confirmationDetail->momentumTimestamp;
        }

        $account->last_active_at = $accountBlockDTO->confirmationDetail->momentumTimestamp;
        $account->save();

        $block = AccountBlock::create([
            'chain_id' => $chain->id,
            'account_id' => $account->id,
            'to_account_id' => $toAccount->id,
            'momentum_id' => $momentum?->id,
            'momentum_acknowledged_id' => $momentumAcknowledged?->id,
            'token_id' => $token?->id,
            'version' => $accountBlockDTO->version,
            'block_type' => $accountBlockDTO->blockType,
            'height' => $accountBlockDTO->height,
            'amount' => $accountBlockDTO->amount,
            'fused_plasma' => $accountBlockDTO->fusedPlasma,
            'base_plasma' => $accountBlockDTO->basePlasma,
            'used_plasma' => $accountBlockDTO->usedPlasma,
            'difficulty' => $accountBlockDTO->difficulty,
            'nonce' => $accountBlockDTO->nonce,
            'hash' => $accountBlockDTO->hash,
            'created_at' => $accountBlockDTO->confirmationDetail?->momentumTimestamp,
        ]);

        if ($accountBlockDTO->descendantBlocks->count()) {
            $this->linkDescendantBlocks($block, $accountBlockDTO->descendantBlocks);
        }

        if ($accountBlockDTO->pairedAccountBlock) {
            $this->linkPairedAccountBlock($block, $accountBlockDTO->pairedAccountBlock);
        }

        if (
            ! empty($accountBlockDTO->data) &&
            $accountBlockDTO->toAddress !== config('explorer.burn_address') &&
            in_array($block->block_type, [
                AccountBlockTypesEnum::SEND,
                AccountBlockTypesEnum::CONTRACT_SEND,
            ], true)
        ) {
            $this->linkBlockData($block, $accountBlockDTO);
        }

        AccountBlockInserted::dispatch($block, $accountBlockDTO);
    }

    private function linkDescendantBlocks(AccountBlock $parentBlock, Collection $descendants): void
    {
        $descendants->each(function ($descendant) use ($parentBlock) {
            $child = AccountBlock::firstWhere('hash', $descendant->hash);

            if (! $child) {
                return;
            }

            $child->parent_id = $parentBlock->id;
            $child->save();

        });
    }

    private function linkPairedAccountBlock(AccountBlock $block, AccountBlockDTO $pairedAccountBlockDTO): void
    {
        Log::debug('Link paired account block', [
            'block' => $block->hash,
            'pair' => $pairedAccountBlockDTO->hash,
        ]);

        $pairedAccountBlock = AccountBlock::firstWhere('hash', $pairedAccountBlockDTO->hash);

        if (! $pairedAccountBlock) {
            return;
        }

        $pairedAccountBlock->paired_account_block_id = $block->id;
        $pairedAccountBlock->save();

        $block->paired_account_block_id = $pairedAccountBlock->id;
        $block->save();

        Log::debug('Link paired account block - link found');
    }

    /**
     * @throws DecodeException
     */
    private function linkBlockData(AccountBlock $accountBlock, AccountBlockDTO $accountBlockDTO): void
    {
        Log::debug('Insert Account Block Data', [
            'hash' => $accountBlockDTO->hash,
        ]);

        $encodedData = base64_decode($accountBlockDTO->data);
        $fingerprint = Utilities::getDataFingerprint($encodedData);
        $contractName = $accountBlock->toAccount->contract?->name ?: 'Common';
        $contractMethod = ContractMethod::whereRelation('contract', 'name', $contractName)
            ->where('fingerprint', $fingerprint)
            ->first();

        if ($contractMethod) {
            $accountBlock->contract_method_id = $contractMethod->id;
            $accountBlock->save();

            $decodedData = app(ZenonSdk::class)->abiDecode($contractMethod, $encodedData);
        }

        $accountBlock->data()->create([
            'raw' => $accountBlockDTO->data,
            'decoded' => $decodedData ?? null,
        ]);
    }
}
