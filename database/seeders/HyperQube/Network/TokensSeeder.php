<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube\Network;

use App\Models\Nom\Token;
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

        $chain = app('currentChain');
        $tokens = Storage::json('json/hqz/genesis.json')['TokenConfig']['Tokens'];

        collect($tokens)->each(function ($tokenData) use ($chain) {
            Token::insert([
                'chain_id' => $chain->id,
                'owner_id' => load_account($tokenData['owner'])->id,
                'name' => $tokenData['tokenName'],
                'symbol' => $tokenData['tokenSymbol'],
                'domain' => $tokenData['tokenDomain'],
                'token_standard' => $tokenData['tokenStandard'],
                'total_supply' => $tokenData['totalSupply'],
                'initial_supply' => $tokenData['totalSupply'],
                'max_supply' => $tokenData['maxSupply'],
                'decimals' => $tokenData['decimals'],
                'is_burnable' => $tokenData['isBurnable'],
                'is_mintable' => $tokenData['isMintable'],
                'is_utility' => $tokenData['isUtility'],
                'created_at' => $chain->created_at->format('Y-m-d H:i:s'),
            ]);
        });
    }
}
