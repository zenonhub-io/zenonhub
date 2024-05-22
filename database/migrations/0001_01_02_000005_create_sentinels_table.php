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
        Schema::create('nom_sentinels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('owner_id')->references('id')->on('nom_accounts');
            $table->timestamp('created_at')->index();
            $table->timestamp('revoked_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_sentinels');
    }
};
