<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

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

        $chainId = app('currentChain')->id;

        Account::insert([
            'chain_id' => $chainId,
            'address' => config('explorer.empty_address'),
            'name' => 'Empty address',
            'is_embedded_contract' => false,
        ]);

        foreach (EmbeddedContractsEnum::cases() as $address) {
            Account::insert([
                'chain_id' => $chainId,
                'address' => $address->value,
                'name' => $address->label(),
                'is_embedded_contract' => true,
            ]);
        }
    }
}
