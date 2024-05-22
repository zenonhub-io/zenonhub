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
        Schema::create('nom_plasma', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('from_account_id')->references('id')->on('nom_accounts');
            $table->foreignId('to_account_id')->references('id')->on('nom_accounts');
            $table->foreignId('account_block_id')->references('id')->on('nom_account_blocks');
            $table->string('amount')->index();
            $table->string('hash');
            $table->timestamp('started_at')->index();
            $table->timestamp('ended_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_plasma');
    }
};
