<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Classes\Utilities;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Database\Seeder;

class TokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ZNN
        Token::insert([
            'chain_id' => Utilities::loadChain()->id,
            'owner_id' => Account::findByAddress('z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0')->id,
            'name' => 'ZNN',
            'symbol' => 'ZNN',
            'domain' => 'zenon.network',
            'token_standard' => 'zts1znnxxxxxxxxxxxxx9z4ulx',
            'total_supply' => 0,
            'max_supply' => 9007199254740991,
            'decimals' => 8,
            'is_burnable' => true,
            'is_mintable' => true,
            'is_utility' => true,
            'created_at' => '2021-11-24 12:00:00',
        ]);

        // QSR
        Token::insert([
            'chain_id' => Utilities::loadChain()->id,
            'owner_id' => Account::findByAddress('z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0')->id,
            'name' => 'QSR',
            'symbol' => 'QSR',
            'domain' => 'zenon.network',
            'token_standard' => 'zts1qsrxxxxxxxxxxxxxmrhjll',
            'total_supply' => 0,
            'max_supply' => 9007199254740991,
            'decimals' => 8,
            'is_burnable' => true,
            'is_mintable' => true,
            'is_utility' => true,
            'created_at' => '2021-11-24 12:00:00',
        ]);
    }
}
