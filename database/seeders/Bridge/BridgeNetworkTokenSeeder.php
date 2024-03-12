<?php

declare(strict_types=1);

namespace Database\Seeders\Bridge;

use App\Classes\Utilities;
use App\Models\Nom\BridgeNetwork;
use App\Services\ZenonSdk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class BridgeNetworkTokenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $znn = App::make(ZenonSdk::class);
        $bridgeNetworks = $znn->bridge->getAllNetworks()['data']->list;

        foreach ($bridgeNetworks as $bridgeNetwork) {

            $network = BridgeNetwork::where('chain_identifier', $bridgeNetwork->chainId)
                ->where('network_class', $bridgeNetwork->networkClass)
                ->first();

            foreach ($bridgeNetwork->tokenPairs as $tokenPair) {
                $token = Utilities::loadToken($tokenPair->tokenStandard);
                $network->tokens()->updateOrCreate([
                    'token_id' => $token->id,
                ], [
                    'token_address' => $tokenPair->tokenAddress,
                    'min_amount' => $tokenPair->minAmount,
                    'fee_percentage' => $tokenPair->feePercentage,
                    'redeem_delay' => $tokenPair->redeemDelay,
                    'metadata' => json_decode($tokenPair->metadata),
                    'is_bridgeable' => $tokenPair->bridgeable,
                    'is_redeemable' => $tokenPair->redeemable,
                    'is_owned' => $tokenPair->owned,
                ]);
            }
        }
    }
}
