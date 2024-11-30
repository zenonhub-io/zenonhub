<?php

declare(strict_types=1);

namespace App\Factories;

use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountBlock;
use Illuminate\Database\Eloquent\Builder;

class AccountBlockModelFactory
{
    public static function create(Account $account): AccountBlock|Builder
    {
        return match ($account->address) {
            EmbeddedContractsEnum::ACCELERATOR->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_accelerator_contract';
            },
            EmbeddedContractsEnum::BRIDGE->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_bridge_contract';
            },
            EmbeddedContractsEnum::HTLC->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_htlc_contract';
            },
            EmbeddedContractsEnum::LIQUIDITY->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_liquidity_contract';
            },
            EmbeddedContractsEnum::PILLAR->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_pillar_contract';
            },
            EmbeddedContractsEnum::PLASMA->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_plasma_contract';
            },
            EmbeddedContractsEnum::PTLC->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_ptlc_contract';
            },
            EmbeddedContractsEnum::SENTINEL->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_sentinel_contract';
            },
            EmbeddedContractsEnum::SPORK->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_spork_contract';
            },
            EmbeddedContractsEnum::STAKE->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_stake_contract';
            },
            EmbeddedContractsEnum::SWAP->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_swap_contract';
            },
            EmbeddedContractsEnum::TOKEN->value => new class extends AccountBlock
            {
                protected $table = 'view_nom_account_blocks_token_contract';
            },
            default => $account->blocks(),
        };
    }
}
