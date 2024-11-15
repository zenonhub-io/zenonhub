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
        Schema::create('nom_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('owner_id')->references('id')->on('nom_accounts');
            $table->string('name')->index();
            $table->string('symbol')->index();
            $table->string('domain');
            $table->string('token_standard')->unique();
            $table->string('total_supply')->default(0);
            $table->string('max_supply')->default(0);
            $table->integer('decimals');
            $table->boolean('is_burnable')->default(1);
            $table->boolean('is_mintable')->default(1);
            $table->boolean('is_utility')->default(1);
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('updated_at')->nullable()->index();
        });

        Schema::create('nom_token_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('currency_id')->references('id')->on('nom_currencies');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->decimal('price', 24, 16)->index();
            $table->timestamp('timestamp')->nullable();
        });

        Schema::create('nom_token_stat_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->string('daily_minted')->default(0);
            $table->string('daily_burned')->default(0);
            $table->string('total_supply')->default(0);
            $table->string('total_holders')->default(0);
            $table->string('total_transactions')->default(0);
            $table->string('total_transferred')->default(0);
            $table->string('total_locked')->default(0);
            $table->string('total_wrapped')->default(0);
            $table->date('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_token_stat_histories');
        Schema::dropIfExists('nom_token_prices');
        Schema::dropIfExists('nom_tokens');
    }
};
