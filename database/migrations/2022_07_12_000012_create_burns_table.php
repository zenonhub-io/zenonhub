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
        Schema::create('nom_token_burns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks')->nullOnDelete();
            $table->bigInteger('amount')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nom_token_burns');
    }
};
