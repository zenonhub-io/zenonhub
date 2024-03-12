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
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->string('name')->index();
            $table->string('symbol')->index();
            $table->string('domain');
            $table->string('token_standard')->index();
            $table->string('total_supply')->default(0)->index();
            $table->string('max_supply')->default(0)->index();
            $table->integer('decimals');
            $table->boolean('is_burnable')->default(1);
            $table->boolean('is_mintable')->default(1);
            $table->boolean('is_utility')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_tokens');
    }
};
