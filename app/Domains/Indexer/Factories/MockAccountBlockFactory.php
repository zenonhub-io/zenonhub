<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Factories;

use App\Domains\Indexer\DataTransferObjects\MockAccountBlockDTO;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Services\ZenonSdk;

class MockAccountBlockFactory
{
    public static function create(MockAccountBlockDTO $mockAccountBlockDTO): AccountBlock
    {
        $block = AccountBlock::create([
            'chain_id' => $mockAccountBlockDTO->momentum->chain_id,
            'account_id' => $mockAccountBlockDTO->account->id,
            'to_account_id' => $mockAccountBlockDTO->toAccount->id,
            'momentum_id' => $mockAccountBlockDTO->momentum->id,
            'momentum_acknowledged_id' => $mockAccountBlockDTO->momentumAcknowledged->id,
            'token_id' => $mockAccountBlockDTO->token?->id,
            'contract_method_id' => $mockAccountBlockDTO->contractMethod?->id,
            'version' => 1,
            'block_type' => $mockAccountBlockDTO->blockType,
            'height' => $mockAccountBlockDTO->height,
            'amount' => $mockAccountBlockDTO->amount,
            'fused_plasma' => 0,
            'base_plasma' => 0,
            'used_plasma' => 0,
            'difficulty' => 0,
            'nonce' => 0,
            'hash' => hash('sha256', $mockAccountBlockDTO->toJson()),
            'created_at' => $mockAccountBlockDTO->momentum->created_at,
        ]);

        if ($mockAccountBlockDTO->contractMethod) {

            $data = $mockAccountBlockDTO->data ?: [];
            $encodedData = app(ZenonSdk::class)
                ->abiEncode($mockAccountBlockDTO->contractMethod, array_values($data));

            $block->data()->create([
                'raw' => base64_encode($encodedData ?: 'null'),
                'decoded' => $mockAccountBlockDTO->data,
            ]);
        }

        return $block;
    }
}
