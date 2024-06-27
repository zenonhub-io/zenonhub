<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Token;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class TokensSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Token::truncate();
        Schema::enableForeignKeyConstraints();

        $chainId = app('currentChain')->id;
        $tokens = Storage::json('nom-json/genesis/genesis.json')['TokenConfig']['Tokens'];

        collect($tokens)->each(function ($tokenData) use ($chainId) {
            Token::insert([
                'chain_id' => $chainId,
                'owner_id' => load_account($tokenData['owner'])->id,
                'name' => $tokenData['tokenName'],
                'symbol' => $tokenData['tokenSymbol'],
                'domain' => $tokenData['tokenDomain'],
                'token_standard' => $tokenData['tokenStandard'],
                'total_supply' => $tokenData['totalSupply'],
                'max_supply' => $tokenData['maxSupply'],
                'decimals' => $tokenData['decimals'],
                'is_burnable' => $tokenData['isBurnable'],
                'is_mintable' => $tokenData['isMintable'],
                'is_utility' => $tokenData['isUtility'],
                'created_at' => '2021-11-24 12:00:00',
            ]);
        });
    }
}
