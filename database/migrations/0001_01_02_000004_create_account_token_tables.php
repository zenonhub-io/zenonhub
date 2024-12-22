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
        Schema::create('nom_account_tokens', function (Blueprint $table) {
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->string('balance')->default(0)->index();
            $table->timestamp('updated_at')->index();

            $table->unique(['account_id', 'token_id'], 'unique_account_token_id');
            $table->index(['account_id', 'token_id', 'balance'], 'account_token_balance_id');
        });

        Schema::create('nom_account_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->string('type')->index();
            $table->string('amount')->default(0)->index();
            $table->timestamp('created_at')->index();

            $table->index(['account_id', 'type'], 'account_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_account_rewards');
        Schema::dropIfExists('nom_account_tokens');
    }
};
