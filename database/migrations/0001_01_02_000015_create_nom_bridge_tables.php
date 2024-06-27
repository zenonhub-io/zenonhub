<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_bridge_admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->foreignId('nominated_by_id')->nullable()->references('id')->on('nom_accounts');
            $table->timestamp('nominated_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
        });

        Schema::create('nom_bridge_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->timestamp('nominated_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
        });

        Schema::create('nom_bridge_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->bigInteger('chain_identifier');
            $table->bigInteger('network_class');
            $table->string('name')->index();
            $table->string('contract_address');
            $table->text('meta_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('nom_bridge_network_tokens', function (Blueprint $table) {
            $table->foreignId('bridge_network_id')->references('id')->on('nom_bridge_networks');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
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

        Schema::create('nom_bridge_wraps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridge_network_id')->references('id')->on('nom_bridge_networks');
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->foreignId('account_block_id')->references('id')->on('nom_account_blocks');
            $table->string('to_address');
            $table->string('signature')->nullable();
            $table->string('amount')->default(0);
            $table->timestamps();
        });

        Schema::create('nom_bridge_unwraps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bridge_network_id')->references('id')->on('nom_bridge_networks');
            $table->foreignId('to_account_id')->references('id')->on('nom_accounts');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->foreignId('account_block_id')->references('id')->on('nom_account_blocks');
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

    public function down(): void
    {
        Schema::dropIfExists('nom_bridge_unwraps');
        Schema::dropIfExists('nom_bridge_wraps');
        Schema::dropIfExists('nom_bridge_network_tokens');
        Schema::dropIfExists('nom_bridge_networks');
        Schema::dropIfExists('nom_bridge_guardians');
        Schema::dropIfExists('nom_bridge_admins');
    }
};
