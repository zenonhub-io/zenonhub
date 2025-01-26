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
        Schema::create('nom_token_mints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('token_id')->references('id')->on('nom_tokens');
            $table->foreignId('issuer_id')->references('id')->on('nom_accounts');
            $table->foreignId('receiver_id')->references('id')->on('nom_accounts');
            $table->foreignId('account_block_id')->references('id')->on('nom_account_blocks');
            $table->string('amount');
            $table->timestamp('created_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_token_mints');
    }
};
