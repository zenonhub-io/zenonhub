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
        Schema::create('nom_token_mints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->foreignId('issuer_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('receiver_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks')->nullOnDelete();
            $table->bigInteger('amount')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('nom_token_mints');
    }
};
