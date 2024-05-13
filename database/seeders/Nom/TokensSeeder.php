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
        $tokens = Storage::json('nom-json/genesis/genesis.json')['TokenConfig']['Tokens'];

        collect($tokens)->each(function ($token) use ($chainId) {
            Token::insert([
                'chain_id' => $chainId,
                'owner_id' => load_account($token['owner'])->id,
                'name' => $token['tokenName'],
                'symbol' => $token['tokenSymbol'],
                'domain' => $token['tokenDomain'],
                'token_standard' => $token['tokenStandard'],
                'total_supply' => $token['totalSupply'],
                'max_supply' => $token['maxSupply'],
                'decimals' => $token['decimals'],
                'is_burnable' => $token['isBurnable'],
                'is_mintable' => $token['isMintable'],
                'is_utility' => $token['isUtility'],
                'created_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
