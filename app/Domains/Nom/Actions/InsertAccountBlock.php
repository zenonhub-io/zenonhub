<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\DataTransferObjects\AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumContentDTO;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Events\AccountBlockInserted;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\ZenonSdk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class InsertAccountBlock
{
    /**
     * @throws ZenonRpcException
     */
    public function execute(MomentumContentDTO $momentumContentDTO): void
    {
        Log::debug('Insert Account Block', [
            'hash' => $momentumContentDTO->hash,
        ]);

        $znn = app(ZenonSdk::class);
        $blockData = $znn->getAccountBlockByHash($momentumContentDTO->hash);
        $block = AccountBlock::findBy('hash', $blockData->hash);

        if ($block) {
            return;
        }

        $chain = load_chain();
        $account = load_account($blockData->address);
        $toAccount = load_account($blockData->toAddress);
        $token = load_token($blockData->token?->tokenStandard);
        $momentum = Momentum::findBy('hash', $blockData->confirmationDetail->momentumHash, true);
        $momentumAcknowledged = Momentum::findBy('hash', $blockData->momentumAcknowledged->hash, true);

        if (! $account->public_key) {
            $account->public_key = $blockData->publicKey;
            $account->save();
        }

        if (! $account->first_active_at) {
            $account->first_active_at = $blockData->confirmationDetail->momentumTimestamp;
            $account->save();
        }

        $block = AccountBlock::create([
            'chain_id' => $chain->id,
            'account_id' => $account->id,
            'to_account_id' => $toAccount->id,
            'momentum_id' => $momentum->id,
            'momentum_acknowledged_id' => $momentumAcknowledged->id,
            'token_id' => $token?->id,
            'version' => $blockData->version,
            'block_type' => $blockData->blockType,
            'height' => $blockData->height,
            'amount' => $blockData->amount,
            'fused_plasma' => $blockData->fusedPlasma,
            'base_plasma' => $blockData->basePlasma,
            'used_plasma' => $blockData->usedPlasma,
            'difficulty' => $blockData->difficulty,
            'nonce' => $blockData->nonce,
            'hash' => $blockData->hash,
            'created_at' => $blockData->confirmationDetail?->momentumTimestamp,
        ]);

        if ($blockData->descendantBlocks->count()) {
            $this->linkDescendantBlocks($block, $blockData->descendantBlocks);
        }

        if ($blockData->pairedAccountBlock) {
            $this->linkPairedAccountBlock($block, $blockData->pairedAccountBlock);
        }

        if (! empty($blockData->data) && in_array($block->block_type->value, [
            AccountBlockTypesEnum::SEND->value,
            AccountBlockTypesEnum::CONTRACT_SEND->value,
        ], true)) {
            $this->linkBlockData($block, $blockData);
        }

        AccountBlockInserted::dispatch($block, $blockData);
    }

    private function linkDescendantBlocks(AccountBlock $parentBlock, Collection $descendants): void
    {
        $descendants->each(function ($descendant) use ($parentBlock) {
            $child = AccountBlock::findBy('hash', $descendant->hash);

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

        $pairedAccountBlock = AccountBlock::findBy('hash', $pairedAccountBlockDTO->hash);

        if (! $pairedAccountBlock) {
            return;
        }

        $pairedAccountBlock->paired_account_block_id = $block->id;
        $pairedAccountBlock->save();

        $block->paired_account_block_id = $pairedAccountBlock->id;
        $block->save();

        Log::debug('Link paired account block - link found');
    }

    private function linkBlockData(AccountBlock $block, AccountBlockDTO $accountBlockDTO): void
    {
        Log::debug('Insert Account Block Data', [
            'hash' => $accountBlockDTO->hash,
        ]);

        $block->data()->create([
            'raw' => $accountBlockDTO->data,
        ]);
    }
}
