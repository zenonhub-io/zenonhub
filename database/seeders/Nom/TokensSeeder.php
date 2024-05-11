<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Token;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class TokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chainId = app('currentChain')->id;
        $tokens = Storage::json('nom-json/genesis/tokens.json');

        foreach ($tokens as $token) {
            Token::insert([
                'chain_id' => $chainId,
                'owner_id' => load_account($token['owner'])->id,
                'name' => $token['name'],
                'symbol' => $token['symbol'],
                'domain' => $token['domain'],
                'token_standard' => $token['token_standard'],
                'total_supply' => $token['total_supply'],
                'max_supply' => $token['max_supply'],
                'decimals' => 8,
                'is_burnable' => true,
                'is_mintable' => true,
                'is_utility' => true,
                'created_at' => '2021-11-24 12:00:00',
            ]);
        }
    }
}
