<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_bridge_network_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridge_network_id')->nullable()->references('id')->on('nom_bridge_networks')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->string('token_address');
            $table->string('min_amount');
            $table->integer('fee_percentage');
            $table->integer('redeem_delay');
            $table->text('metadata')->nullable();
            $table->boolean('is_bridgeable')->default(0);
            $table->boolean('is_redeemable')->default(0);
            $table->boolean('is_owned')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_bridge_network_tokens');
    }
};
