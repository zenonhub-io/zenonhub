<?php

declare(strict_types=1);

namespace Database\Seeders\Nom\Network;

use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\Account;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class AccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Account::truncate();
        Schema::enableForeignKeyConstraints();

        $chain = app('currentChain');

        Account::updateOrCreate([
            'chain_id' => $chain->id,
            'address' => config('explorer.burn_address'),
        ], [
            'name' => 'Burn Address',
            'is_embedded_contract' => false,
        ]);

        foreach (EmbeddedContractsEnum::cases() as $address) {
            Account::updateOrCreate([
                'chain_id' => $chain->id,
                'address' => $address->value,
            ], [
                'name' => $address->label(),
                'is_embedded_contract' => true,
            ]);
        }

        Account::updateOrCreate([
            'chain_id' => $chain->id,
            'address' => config('nom.bridge.initialBridgeAdmin'),
        ], [
            'name' => 'Bridge admin',
            'is_embedded_contract' => false,
        ]);
    }
}
