<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('momentum_id')->nullable()->references('id')->on('nom_momentums')->cascadeOnDelete();
            $table->foreignId('momentum_acknowledged_id')->nullable()->references('id')->on('nom_momentums')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->references('id')->on('nom_account_blocks')->cascadeOnDelete();
            $table->foreignId('paired_account_block_id')->nullable()->references('id')->on('nom_account_blocks')->nullOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->nullOnDelete();
            $table->foreignId('contract_method_id')->nullable()->references('id')->on('nom_contract_methods')->nullOnDelete();
            $table->integer('version');
            $table->integer('block_type')->index();
            $table->bigInteger('height')->index();
            $table->string('amount')->default(0);
            $table->bigInteger('fused_plasma')->default(0);
            $table->bigInteger('base_plasma')->default(0);
            $table->bigInteger('used_plasma')->default(0);
            $table->bigInteger('difficulty')->default(0);
            $table->string('nonce')->nullable();
            $table->string('hash')->unique();
            $table->timestamp('created_at')->nullable();

            $table->index('account_id');
            $table->index('to_account_id');

            $table->index(['account_id', 'to_account_id']);
            $table->index(['account_id', 'token_id']);
            $table->index(['to_account_id', 'token_id']);
        });

        Schema::create('nom_account_block_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks')->cascadeOnDelete();
            $table->text('raw')->nullable();
            $table->text('decoded')->nullable();
            $table->boolean('is_processed')->default(0);
        });

        Artisan::call('nom-db:create-or-update-latest-account-blocks-view');
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
