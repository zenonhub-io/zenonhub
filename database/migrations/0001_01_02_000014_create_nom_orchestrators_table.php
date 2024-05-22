<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_orchestrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->references('id')->on('nom_pillars');
            $table->foreignId('account_id')->references('id')->on('nom_accounts');
            $table->boolean('status')->index()->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_orchestrators');
    }
};
