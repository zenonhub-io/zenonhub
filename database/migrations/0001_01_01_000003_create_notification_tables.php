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
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->index();
            $table->string('category')->index();
            $table->tinyText('description')->nullable();
            $table->text('data')->nullable();
            $table->boolean('is_configurable')->default(0);
            $table->boolean('is_active')->default(0);
        });

        Schema::create('notification_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('type_id')->nullable()->references('id')->on('notification_types')->cascadeOnDelete();
            $table->text('data')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('type_id')->nullable()->references('id')->on('notification_types')->cascadeOnDelete();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_subscriptions');
        Schema::dropIfExists('notification_types');
    }
};
