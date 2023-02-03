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
        Schema::create('nom_account_tokens_pivot', function (Blueprint $table) {
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->bigInteger('balance')->index()->nullable();
            $table->timestamp('updated_at')->index()->nullable();
        });

        Schema::create('nom_account_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->nullOnDelete();
            $table->integer('type')->index();
            $table->bigInteger('amount')->index()->default(0);
            $table->timestamp('created_at')->index()->nullable();
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
        Schema::dropIfExists('nom_account_rewards');
        Schema::dropIfExists('nom_account_tokens_pivot');
        Schema::enableForeignKeyConstraints();
    }
};
