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
        Schema::create('nom_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->string('name')->index();
            $table->string('symbol')->index();
            $table->string('domain');
            $table->string('token_standard')->index();
            $table->bigInteger('total_supply')->index();
            $table->bigInteger('max_supply')->index();
            $table->integer('decimals');
            $table->boolean('is_burnable')->default(true);
            $table->boolean('is_mintable')->default(true);
            $table->boolean('is_utility')->default(true);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nom_tokens');
    }
};
