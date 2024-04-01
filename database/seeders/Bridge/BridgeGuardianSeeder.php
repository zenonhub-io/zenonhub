<?php

declare(strict_types=1);

namespace Database\Seeders\Bridge;

use App\Domains\Nom\Models\BridgeGuardian;
use App\Services\ZenonSdk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class BridgeGuardianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $znn = App::make(ZenonSdk::class);
        $bridgeInfo = $znn->bridge->getSecurityInfo()['data'];

        foreach ($bridgeInfo->guardians as $guardianAddress) {
            $guardian = load_account($guardianAddress);

            BridgeGuardian::query()
                ->updateOrInsert([
                    'account_id' => $guardian->id,
                ], [
                    'nominated_at' => '2023-05-03 10:17:30',
                    'accepted_at' => '2023-05-03 10:17:30',
                ]);
        }
    }
}
