<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() : void
    {
        Schema::create('nom_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->string('address')->unique();
            $table->string('name')->index()->nullable();
            $table->string('public_key')->nullable();
            $table->bigInteger('znn_balance')->index()->default(0);
            $table->bigInteger('qsr_balance')->index()->default(0);
            $table->bigInteger('znn_staked')->index()->default(0);
            $table->bigInteger('qsr_fused')->index()->default(0);
            $table->bigInteger('znn_locked')->index()->default(0);
            $table->bigInteger('qsr_locked')->index()->default(0);
            $table->bigInteger('total_znn_balance')->index()->default(0);
            $table->bigInteger('total_qsr_balance')->index()->default(0);
            $table->bigInteger('total_znn_rewards')->index()->default(0);
            $table->bigInteger('total_qsr_rewards')->index()->default(0);
            $table->bigInteger('total_znn_sent')->index()->default(0);
            $table->bigInteger('total_znn_received')->index()->default(0);
            $table->bigInteger('total_qsr_sent')->index()->default(0);
            $table->bigInteger('total_qsr_received')->index()->default(0);
            $table->boolean('is_embedded_contract')->default(0);
            $table->timestamp('first_active_at')->index()->nullable();
            $table->timestamp('updated_at')->index()->nullable();
        });

        Schema::table('nom_contracts', function (Blueprint $table) {
            $table->foreignId('account_id')->after('chain_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::table('nom_contracts', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });

        Schema::dropIfExists('nom_accounts');
    }
};
