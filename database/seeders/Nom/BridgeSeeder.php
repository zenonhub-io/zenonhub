<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

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

        $adminAccount = load_account(config('nom.bridge.initialBridgeAdmin'), 'Bridge admin');
        BridgeAdmin::query()
            ->updateOrInsert([
                'account_id' => $adminAccount->id,
            ], [
                'nominated_at' => '2023-05-03 10:17:30',
                'accepted_at' => '2023-05-03 10:17:30',
            ]);
    }
}
