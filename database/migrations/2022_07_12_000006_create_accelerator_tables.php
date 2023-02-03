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
        Schema::create('nom_accelerator_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->string('hash')->unique();
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->string('url');
            $table->mediumText('description');
            $table->integer('status')->default(0);
            $table->bigInteger('znn_funds_needed')->default(0);
            $table->bigInteger('qsr_funds_needed')->default(0);
            $table->decimal('znn_price', 12, 4)->default(0)->index();
            $table->decimal('qsr_price', 12, 4)->default(0)->index();
            $table->integer('vote_total')->default(0)->index();
            $table->integer('vote_yes')->default(0)->index();
            $table->integer('vote_no')->default(0)->index();
            $table->timestamp('send_reminders_at')->nullable();
            $table->timestamp('modified_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('nom_accelerator_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accelerator_project_id')->nullable()->references('id')->on('nom_accelerator_projects')->cascadeOnDelete();
            $table->string('hash')->unique();
            $table->string('name')->index();
            $table->string('slug')->index();
            $table->string('url');
            $table->mediumText('description');
            $table->integer('status')->default(0);
            $table->bigInteger('znn_funds_needed')->default(0);
            $table->bigInteger('qsr_funds_needed')->default(0);
            $table->decimal('znn_price', 12, 4)->default(0)->index();
            $table->decimal('qsr_price', 12, 4)->default(0)->index();
            $table->integer('vote_total')->default(0)->index();
            $table->integer('vote_yes')->default(0)->index();
            $table->integer('vote_no')->default(0)->index();
            $table->timestamp('send_reminders_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

        Schema::create('nom_accelerator_project_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accelerator_project_id')->nullable()->references('id')->on('nom_accelerator_projects')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->nullOnDelete();
            $table->boolean('is_yes')->default(0);
            $table->boolean('is_no')->default(0);
            $table->boolean('is_abstain')->default(0);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('nom_accelerator_phase_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('accelerator_phase_id')->nullable()->references('id')->on('nom_accelerator_phases')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars')->nullOnDelete();
            $table->boolean('is_yes')->default(0);
            $table->boolean('is_no')->default(0);
            $table->boolean('is_abstain')->default(0);
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nom_accelerator_phase_votes');
        Schema::dropIfExists('nom_accelerator_project_votes');
        Schema::dropIfExists('nom_accelerator_phases');
        Schema::dropIfExists('nom_accelerator_projects');
        Schema::enableForeignKeyConstraints();
    }
};
