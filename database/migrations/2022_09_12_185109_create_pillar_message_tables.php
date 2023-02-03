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
        Schema::create('pillar_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->cascadeOnDelete();
            $table->string('title')->index();
            $table->text('post');
            $table->string('message');
            $table->string('signature');
            $table->boolean('is_public')->default('1');
            $table->boolean('is_vote')->default('0');
            $table->boolean('is_broadcast')->default('0');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('pillar_message_vote_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_messages_id')->nullable()->references('id')->on('pillar_messages')->cascadeOnDelete();
            $table->string('name')->nullable();
        });

        Schema::create('pillar_message_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_messages_id')->references('id')->on('pillar_messages')->cascadeOnDelete();
            $table->foreignId('vote_option_id')->references('id')->on('pillar_message_vote_options')->cascadeOnDelete();
            $table->foreignId('account_id')->references('id')->on('nom_accounts')->cascadeOnDelete();
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
        Schema::dropIfExists('pillar_message_votes');
        Schema::dropIfExists('pillar_message_vote_options');
        Schema::dropIfExists('pillar_messages');
    }
};
