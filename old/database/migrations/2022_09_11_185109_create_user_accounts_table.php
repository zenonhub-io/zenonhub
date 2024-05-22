<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_nom_accounts_pivot', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts');
            $table->string('nickname')->index()->nullable();
            $table->boolean('is_pillar')->default(0);
            $table->boolean('is_sentinel')->default(0);
            $table->boolean('is_default')->default(1);
            $table->timestamp('verified_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('user_accounts_pivot');
        Schema::enableForeignKeyConstraints();
    }
};
