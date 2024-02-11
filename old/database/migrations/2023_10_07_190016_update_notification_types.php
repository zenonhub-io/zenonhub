<?php

use App\Models\NotificationType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::table('notification_types', function (Blueprint $table) {
            $table->dropColumn('content', 'link');
        });

        Schema::table('notification_types', function (Blueprint $table) {
            $table->renameColumn('type', 'category');
        });

        Schema::disableForeignKeyConstraints();
        DB::table('notification_subscriptions')->truncate();
        DB::table('notification_types')->truncate();
        Schema::enableForeignKeyConstraints();

        NotificationType::insert([
            [
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

    public function down() : void
    {
        Schema::table('notification_types', function (Blueprint $table) {
            $table->string('link')->nullable()->after('description');
            $table->tinyText('content')->nullable()->after('description');
        });

        Schema::table('notification_types', function (Blueprint $table) {
            $table->renameColumn('category', 'type');
        });
    }
};
