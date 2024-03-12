<?php

declare(strict_types=1);

namespace Database\Seeders\Bridge;

use App\Classes\Utilities;
use App\Models\Nom\BridgeNetwork;
use App\Services\ZenonSdk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class BridgeNetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chain = Utilities::loadChain();
        $znn = App::make(ZenonSdk::class);
        $bridgeNetworks = $znn->bridge->getAllNetworks()['data']->list;

        foreach ($bridgeNetworks as $bridgeNetwork) {
            BridgeNetwork::create([
                'chain_id' => $chain->id,
                'chain_identifier' => $bridgeNetwork->chainId,
                'network_class' => $bridgeNetwork->networkClass,
                'name' => $bridgeNetwork->name,
                'contract_address' => $bridgeNetwork->contractAddress,
                'meta_data' => json_decode($bridgeNetwork->metadata),
            ]);
        }
    }
}
