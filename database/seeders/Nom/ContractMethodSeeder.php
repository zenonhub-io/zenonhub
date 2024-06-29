<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Actions\UpdateContractMethods;
use App\Domains\Nom\Models\Contract;
use App\Domains\Nom\Models\ContractMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ContractMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Contract::truncate();
        ContractMethod::truncate();
        Schema::enableForeignKeyConstraints();

        UpdateContractMethods::run();
    }
}
