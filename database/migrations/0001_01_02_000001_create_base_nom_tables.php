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
        Schema::create('nom_chains', function (Blueprint $table) {
            $table->id();
            $table->integer('chain_identifier');
            $table->integer('version');
            $table->string('name');
            $table->boolean('is_active');
            $table->timestamp('created_at');
        });

        Schema::create('nom_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains')->cascadeOnDelete();
            $table->string('name')->index();
        });

        Schema::create('nom_contract_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->references('id')->on('nom_contracts')->cascadeOnDelete();
            $table->string('name')->index();
            $table->string('signature');
            $table->string('fingerprint')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nom_contract_methods');
        Schema::dropIfExists('nom_contracts');
        Schema::dropIfExists('nom_chains');
        Schema::enableForeignKeyConstraints();
    }
};
