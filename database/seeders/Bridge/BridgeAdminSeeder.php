<?php

namespace Database\Seeders\Bridge;

use App\Classes\Utilities;
use App\Models\Nom\BridgeAdmin;
use App\Services\ZenonSdk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class BridgeAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $znn = App::make(ZenonSdk::class);
        $bridgeInfo = $znn->bridge->getBridgeInfo()['data'];
        $adminAccount = Utilities::loadAccount($bridgeInfo->administrator, 'Bridge admin');

        BridgeAdmin::query()
            ->updateOrInsert([
                'account_id' => $adminAccount->id,
            ], [
                'nominated_at' => '2023-05-03 10:17:30',
                'accepted_at' => '2023-05-03 10:17:30',
            ]);
    }
}
