<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_profiles', function (Blueprint $table) {
            $table->id();
            $table->morphs('profileable');
            $table->string('name')->nullable();
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('x')->nullable();
            $table->string('telegram')->nullable();
            $table->string('github')->nullable();
            $table->string('discord')->nullable();
            $table->string('medium')->nullable();
            $table->timestamps();
        });

        Schema::create('social_profile_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_profile_id')->references('id')->on('social_profiles')->cascadeOnDelete();
            $table->string('status')->nullable();
            $table->integer('total_likes')->nullable();
            $table->integer('total_views')->nullable();
            $table->integer('total_comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('social_profiles');
        Schema::dropIfExists('social_profile_statuses');
        Schema::enableForeignKeyConstraints();
    }
};
