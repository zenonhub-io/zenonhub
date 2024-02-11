<?php

use Database\Seeders\Bridge\BridgeAdminSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create('nom_bridge_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->timestamp('nominated_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
        });

        if (! app()->environment('testing')) {
            $seeder = new BridgeAdminSeeder;
            $seeder->run();
        }
    }

    public function down() : void
    {
        Schema::dropIfExists('nom_bridge_admins');
    }
};
