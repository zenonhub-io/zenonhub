<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nom_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->string('address')->unique();
            $table->string('name')->index()->nullable();
            $table->string('public_key')->nullable();
            $table->string('znn_balance')->index()->default(0);
            $table->string('qsr_balance')->index()->default(0);
            $table->string('znn_staked')->index()->default(0);
            $table->string('qsr_fused')->index()->default(0);
            $table->string('znn_rewards')->index()->default(0);
            $table->string('qsr_rewards')->index()->default(0);
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
    public function down(): void
    {
        Schema::table('nom_contracts', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });

        Schema::dropIfExists('nom_accounts');
    }
};
