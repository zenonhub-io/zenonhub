<?php

declare(strict_types=1);

namespace Database\Seeders\Site;

use App\Models\NotificationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class NotificationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        NotificationType::truncate();
        Schema::enableForeignKeyConstraints();

        NotificationType::insert([
            [
                'name' => 'Site News',
                'code' => 'zenonhub-updates',
                'category' => 'zenonhub',
                'description' => 'Important Zenon Hub updates and news',
                'is_configurable' => false,
                'is_active' => true,
            ], [
                'name' => 'Accelerator Z',
                'code' => 'network-az',
                'category' => 'network',
                'description' => 'A new project or phase is created',
                'is_configurable' => false,
                'is_active' => true,
            ], [
                'name' => 'Pillar',
                'code' => 'network-pillar',
                'category' => 'network',
                'description' => 'A pillar is registered, updated or revoked',
                'is_configurable' => false,
                'is_active' => true,
            ], [
                'name' => 'Sentinel',
                'code' => 'network-sentinel',
                'category' => 'network',
                'description' => 'A sentinel is registered or revoked',
                'is_configurable' => false,
                'is_active' => true,
            ], [
                'name' => 'Token',
                'code' => 'network-token',
                'category' => 'network',
                'description' => 'A new token is registered',
                'is_configurable' => false,
                'is_active' => true,
            ], [
                'name' => 'Bridge',
                'code' => 'network-bridge',
                'category' => 'network',
                'description' => 'The bridge status changes or admin commands are issued',
                'is_configurable' => false,
                'is_active' => false,
            ],
        ]);
    }
}
