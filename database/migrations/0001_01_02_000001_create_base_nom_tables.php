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
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains');
            $table->string('name')->index();
        });

        Schema::create('nom_contract_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->references('id')->on('nom_contracts');
            $table->string('name')->index();
            $table->string('signature');
            $table->string('fingerprint')->index();
        });

        Schema::create('nom_votes', function (Blueprint $table) {
            $table->id();
            $table->morphs('votable');
            $table->integer('vote')->default(0)->index();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('nom_time_challenges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->nullable()->references('id')->on('nom_chains');
            $table->foreignId('contract_method_id')->nullable()->references('id')->on('nom_contract_methods');
            $table->string('hash')->default(0);
            $table->bigInteger('delay')->default(0);
            $table->bigInteger('start_height')->nullable();
            $table->bigInteger('end_height')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('nom_currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('code')->index();
            $table->string('symbol')->nullable();
            $table->string('icon')->nullable();
            $table->boolean('is_default')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nom_currencies');
        Schema::dropIfExists('nom_time_challenges');
        Schema::dropIfExists('nom_votes');
        Schema::dropIfExists('nom_contract_methods');
        Schema::dropIfExists('nom_contracts');
        Schema::dropIfExists('nom_chains');
        Schema::enableForeignKeyConstraints();
    }
};
