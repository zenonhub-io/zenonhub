<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\DataTransferObjects\AccountBlockData as AccountBlockDTO;
use App\Domains\Nom\DataTransferObjects\MomentumContentData as MomentumContentDTO;
use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Events\AccountBlockInserted;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Utilities;
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
        $momentum = Momentum::findBy('hash', $blockData->confirmationDetail?->momentumHash);
        $momentumAcknowledged = Momentum::findBy('hash', $blockData->momentumAcknowledged?->hash);

        if (! $block) {
            $chain = load_chain();
            $account = load_account($blockData->address);
            $toAccount = load_account($blockData->toAddress);
            $token = load_token($blockData->token?->tokenStandard);

            if (! $account->public_key) {
                $account->public_key = $blockData->publicKey;
                $account->save();
            }

            if (! $account->first_active_at) {
                $account->first_active_at = $blockData->confirmationDetail?->momentumTimestamp;
                $account->save();
            }

            $block = AccountBlock::create([
                'chain_id' => $chain->id,
                'account_id' => $account->id,
                'to_account_id' => $toAccount->id,
                'momentum_id' => $momentum?->id,
                'momentum_acknowledged_id' => $momentumAcknowledged?->id,
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
                $this->processBlockData($block, $blockData);
            }

            AccountBlockInserted::dispatch($block, $blockData);
        } else {
            $block->momentum_id = ($momentum->id ?? $block->momentum_id);
            $block->momentum_acknowledged_id = $momentumAcknowledged?->id;
            $block->height = $blockData->height;
            $block->save();
        }
    }

    private function linkDescendantBlocks(AccountBlock $parentBlock, Collection $descendants): void
    {
        $descendants->each(function ($descendant) use ($parentBlock) {
            $child = AccountBlock::findBy('hash', $descendant->hash);

            // TODO
            if (! $child) {
                dd('no child', [
                    'parent' => $parentBlock->hash,
                    'child' => $descendant->hash,
                ]);
            }

            $child->parent_id = $parentBlock->id;
            $child->save();
        });
    }

    private function linkPairedAccountBlock(AccountBlock $block, AccountBlockDTO $pairedAccountBlockDTO): void
    {
        $pairedAccountBlock = AccountBlock::findBy('hash', $pairedAccountBlockDTO->hash);

        if ($pairedAccountBlock) {
            $block->paired_account_block_id = $pairedAccountBlock->id;
            $block->save();

            $pairedAccountBlock->paired_account_block_id = $block->id;
            $pairedAccountBlock->save();
        }
    }

    private function processBlockData(AccountBlock $block, AccountBlockDTO $accountBlockDTO): void
    {
        Log::debug('Insert Account Block Data', [
            'hash' => $accountBlockDTO->hash,
        ]);

        $decodedData = null;
        $data = base64_decode($accountBlockDTO->data);
        $fingerprint = Utilities::getDataFingerprint($data);
        $contractMethod = ContractMethod::whereRelation('contract', 'name', $block->toAccount->contract?->name)
            ->where('fingerprint', $fingerprint)
            ->first();

        // Fallback for common methods (not related to a specific account)
        if (! $contractMethod) {
            $contractMethod = ContractMethod::whereRelation('contract', 'name', 'Common')
                ->where('fingerprint', $fingerprint)
                ->first();
        }

        if ($contractMethod) {
            $block->contract_method_id = $contractMethod->id;
            $block->save();

            $contractName = ucfirst(strtolower($contractMethod->contract->name));
            $embeddedContract = "DigitalSloth\ZnnPhp\Abi\Contracts\\" . $contractName;

            if (class_exists($embeddedContract)) {
                $embeddedContract = new $embeddedContract;
                $decoded = $embeddedContract->decode($contractMethod->name, $data);
                $parameters = $embeddedContract->getParameterNames($contractMethod->name);

                if ($decoded && $parameters) {
                    $parameters = explode(',', $parameters);

                    $decodedData = array_combine(
                        $parameters,
                        $decoded
                    );
                }
            }
        }

        $block->data()->create([
            'raw' => $accountBlockDTO->data,
            'decoded' => $decodedData,
        ]);
    }
}
