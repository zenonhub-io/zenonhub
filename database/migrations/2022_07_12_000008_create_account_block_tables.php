<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nom_account_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('momentum_id')->nullable()->references('id')->on('nom_momentums')->cascadeOnDelete();
            $table->foreignId('momentum_acknowledged_id')->nullable()->references('id')->on('nom_momentums')->nullOnDelete();
            $table->foreignId('parent_block_id')->nullable()->references('id')->on('nom_account_blocks')->cascadeOnDelete();
            $table->foreignId('paired_account_block_id')->nullable()->references('id')->on('nom_account_blocks')->nullOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->nullOnDelete();
            $table->integer('version');
            $table->integer('chain_identifier');
            $table->integer('block_type')->index();
            $table->bigInteger('height')->index();
            $table->bigInteger('amount')->default(0);
            $table->bigInteger('fused_plasma')->default(0);
            $table->bigInteger('base_plasma')->default(0);
            $table->bigInteger('used_plasma')->default(0);
            $table->bigInteger('difficulty')->default(0);
            $table->string('hash')->unique();
            $table->string('nonce');
            $table->string('public_key')->nullable();
            $table->string('signature')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('nom_account_block_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks')->cascadeOnDelete();
            $table->foreignId('contract_method_id')->nullable()->references('id')->on('nom_contract_methods')->nullOnDelete();
            $table->longText('raw')->nullable();
            $table->longText('decoded')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nom_account_block_data');
        Schema::dropIfExists('nom_account_blocks');
        Schema::enableForeignKeyConstraints();
    }
};
