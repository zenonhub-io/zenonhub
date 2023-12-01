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
            $table->foreignId('network_id')->nullable()->references('id')->on('nom_bridge_networks')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->string('token_address');
            $table->boolean('is_bridgeable')->index();
            $table->boolean('is_redeemable')->index();
            $table->boolean('is_owned')->index();
            $table->bigInteger('min_amount')->index();
            $table->integer('fee_percentage')->index();
            $table->integer('redeem_delay');
            $table->text('metadata');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_bridge_network_tokens');
    }
};
