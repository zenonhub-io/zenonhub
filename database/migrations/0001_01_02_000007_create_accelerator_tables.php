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
        Schema::create('nom_accelerator_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('owner_id')->references('id')->on('nom_accounts');
            $table->string('hash')->unique();
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->string('url');
            $table->mediumText('description');
            $table->integer('status')->default(0);
            $table->bigInteger('znn_requested')->default(0);
            $table->bigInteger('qsr_requested')->default(0);
            $table->bigInteger('znn_paid')->default(0);
            $table->bigInteger('qsr_paid')->default(0);
            $table->bigInteger('znn_remaining')->default(0);
            $table->bigInteger('qsr_remaining')->default(0);
            $table->decimal('znn_price', 12, 4)->nullable();
            $table->decimal('qsr_price', 12, 4)->nullable();
            $table->integer('total_votes')->default(0)->index();
            $table->integer('total_yes_votes')->default(0)->index();
            $table->integer('total_no_votes')->default(0)->index();
            $table->integer('total_abstain_votes')->default(0)->index();
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->nullable()->index();
        });

        Schema::create('nom_accelerator_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->references('id')->on('nom_accelerator_projects');
            $table->string('hash')->unique();
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->string('url');
            $table->mediumText('description');
            $table->integer('status')->default(0);
            $table->integer('phase_number')->default(1);
            $table->bigInteger('znn_requested')->default(0);
            $table->bigInteger('qsr_requested')->default(0);
            $table->decimal('znn_price', 12, 4)->nullable();
            $table->decimal('qsr_price', 12, 4)->nullable();
            $table->integer('total_votes')->default(0)->index();
            $table->integer('total_yes_votes')->default(0)->index();
            $table->integer('total_no_votes')->default(0)->index();
            $table->integer('total_abstain_votes')->default(0)->index();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_accelerator_phases');
        Schema::dropIfExists('nom_accelerator_projects');
    }
};
