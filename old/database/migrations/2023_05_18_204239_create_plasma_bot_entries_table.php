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
        Schema::create('plasma_bot_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->string('address')->index();
            $table->string('hash')->nullable()->index();
            $table->integer('amount');
            $table->boolean('is_confirmed')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() : void
    {
        Schema::dropIfExists('plasma_bot_entries');
    }
};
