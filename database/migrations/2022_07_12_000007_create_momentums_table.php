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
        Schema::create('nom_momentums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producer_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('producer_pillar_id')->nullable()->references('id')->on('nom_pillars')->nullOnDelete();
            $table->integer('version');
            $table->integer('chain_identifier');
            $table->bigInteger('height')->index();
            $table->string('hash')->unique();
            $table->string('public_key')->nullable();
            $table->string('signature')->nullable();
            $table->text('data')->nullable();
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
        Schema::dropIfExists('nom_momentums');
    }
};
