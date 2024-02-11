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
        Schema::create('nom_momentums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('producer_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('producer_pillar_id')->nullable()->references('id')->on('nom_pillars')->nullOnDelete();
            $table->integer('version');
            $table->bigInteger('height')->index();
            $table->string('hash')->unique();
            $table->text('data')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('nom_momentums');
    }
};
