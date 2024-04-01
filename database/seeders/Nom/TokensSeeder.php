<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Token;
use Illuminate\Database\Seeder;

class TokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chain = load_chain();
        $tokens = [
            [
                'owner' => 'z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0',
                'name' => 'ZNN',
                'symbol' => 'ZNN',
                'domain' => 'zenon.network',
                'token_standard' => 'zts1znnxxxxxxxxxxxxx9z4ulx',
            ], [
                'owner' => 'z1qxemdeddedxt0kenxxxxxxxxxxxxxxxxh9amk0',
                'name' => 'QSR',
                'symbol' => 'QSR',
                'domain' => 'zenon.network',
                'token_standard' => 'zts1qsrxxxxxxxxxxxxxmrhjll',
            ],
        ];

        foreach ($tokens as $token) {
            Token::insert([
                'chain_id' => $chain->id,
                'owner_id' => load_account($token['owner'])->id,
                'name' => $token['name'],
                'symbol' => $token['symbol'],
                'domain' => $token['domain'],
                'token_standard' => $token['token_standard'],
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
}
