<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nom_account_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->foreignId('to_account_id')->references('id')->on('nom_accounts');
            $table->foreignId('momentum_id')->references('id')->on('nom_momentums');
            $table->foreignId('momentum_acknowledged_id')->nullable()->references('id')->on('nom_momentums');
            $table->foreignId('parent_id')->nullable()->references('id')->on('nom_account_blocks');
            $table->foreignId('paired_account_block_id')->nullable()->references('id')->on('nom_account_blocks');
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens');
            $table->foreignId('contract_method_id')->nullable()->references('id')->on('nom_contract_methods');
            $table->integer('version')->default(1);
            $table->integer('block_type')->index();
            $table->bigInteger('height')->index();
            $table->string('amount')->default(0);
            $table->bigInteger('fused_plasma')->default(0);
            $table->bigInteger('base_plasma')->default(0);
            $table->bigInteger('used_plasma')->default(0);
            $table->bigInteger('difficulty')->default(0);
            $table->string('nonce')->nullable();
            $table->string('hash')->unique();
            $table->timestamp('created_at')->index();

            $table->index('account_id');
            $table->index('to_account_id');

            $table->index(['account_id', 'to_account_id']);
            $table->index(['account_id', 'token_id']);
            $table->index(['to_account_id', 'token_id']);
            $table->index(['to_account_id', 'token_id', 'amount']);
            $table->index(['account_id', 'token_id', 'amount']);
        });

        Schema::create('nom_account_block_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_block_id')->references('id')->on('nom_account_blocks');
            $table->text('raw');
            $table->text('decoded')->nullable();
            $table->boolean('is_processed')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::statement('DROP VIEW IF EXISTS view_latest_nom_account_blocks');
        Schema::dropIfExists('nom_account_block_data');
        Schema::dropIfExists('nom_account_blocks');
        Schema::enableForeignKeyConstraints();
    }
};
