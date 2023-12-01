<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_bridge_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->bigInteger('network_class');
            $table->string('name')->index();
            $table->string('contract_address');
            $table->text('meta_data');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_bridge_networks');
    }
};
