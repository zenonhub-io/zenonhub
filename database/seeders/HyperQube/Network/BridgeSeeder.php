<?php

declare(strict_types=1);

namespace Database\Seeders\HyperQube\Network;

use App\Models\Nom\Account;
use App\Models\Nom\BridgeAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class BridgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        BridgeAdmin::truncate();
        Schema::enableForeignKeyConstraints();

        $bridgeAdmin = Account::firstWhere('address', config('nom.bridge.initialBridgeAdmin'));

        BridgeAdmin::query()
            ->updateOrInsert([
                'account_id' => $bridgeAdmin->id,
            ], [
                'nominated_at' => '2023-05-03 10:17:30',
                'accepted_at' => '2023-05-03 10:17:30',
            ]);
    }
}
