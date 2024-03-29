<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_orchestrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->cascadeOnDelete();
            $table->boolean('status')->index()->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_orchestrators');
    }
};
