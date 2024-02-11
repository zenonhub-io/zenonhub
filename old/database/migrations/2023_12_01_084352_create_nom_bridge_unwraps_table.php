<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() : void
    {
        Schema::create('nom_bridge_unwraps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridge_network_id')->references('id')->on('nom_bridge_networks')->cascadeOnDelete();
            $table->foreignId('bridge_network_token_id')->references('id')->on('nom_bridge_network_tokens')->cascadeOnDelete();
            $table->foreignId('to_account_id')->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks')->nullOnDelete();
            $table->bigInteger('log_index');
            $table->string('from_address')->nullable();
            $table->string('transaction_hash');
            $table->string('signature')->nullable();
            $table->string('amount');
            $table->timestamp('redeemed_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down() : void
    {
        Schema::dropIfExists('nom_bridge_unwraps');
    }
};
