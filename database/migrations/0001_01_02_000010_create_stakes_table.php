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
        Schema::create('nom_stakes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->foreignId('account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('token_id')->nullable()->references('id')->on('nom_tokens')->cascadeOnDelete();
            $table->string('amount')->default(0)->index();
            $table->bigInteger('duration')->index();
            $table->string('hash')->nullable();
            $table->timestamp('started_at')->index()->nullable();
            $table->timestamp('ended_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_stakes');
    }
};
