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
        Schema::create('nom_sentinels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts');
            $table->integer('revoke_cooldown')->default(0);
            $table->boolean('is_revocable')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('created_at')->nullable()->index();
            $table->timestamp('revoked_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nom_sentinels');
    }
};
