<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Chain;
use App\Domains\Nom\Models\Contract;
use App\Domains\Nom\Models\ContractMethod;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Pillar;
use App\Domains\Nom\Models\Token;
use App\Models\NotificationType;
use Database\Seeders\Nom\AccountBlocksSeeder;
use Database\Seeders\Nom\AccountsSeeder;
use Database\Seeders\Nom\ChainsSeeder;
use Database\Seeders\Nom\ContractMethodSeeder;
use Database\Seeders\Nom\MomentumsSeeder;
use Database\Seeders\Nom\PillarsSeeder;
use Database\Seeders\Nom\TokensSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        NotificationType::truncate();
        Role::truncate();
        Chain::truncate();
        Contract::truncate();
        ContractMethod::truncate();
        Account::truncate();
        Token::truncate();
        Pillar::truncate();
        Momentum::truncate();
        //AccountBlock::truncate();
        Schema::enableForeignKeyConstraints();

        $this->call([
            NotificationTypesSeeder::class,
            UserRolesSeeder::class,
            ChainsSeeder::class,
            ContractMethodSeeder::class,
            AccountsSeeder::class,
            TokensSeeder::class,
            PillarsSeeder::class,
            MomentumsSeeder::class,
            //AccountBlocksSeeder::class,
        ]);
    }
}
