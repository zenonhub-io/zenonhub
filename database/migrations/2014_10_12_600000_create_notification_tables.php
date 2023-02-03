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
        Schema::create('notification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->index();
            $table->string('type')->index();
            $table->tinyText('description')->nullable();
            $table->tinyText('content');
            $table->string('link');
            $table->text('data')->nullable();
            $table->boolean('is_basic')->default(0);
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

        Artisan::call('db:seed', array('--class' => 'NotificationTypesSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('notification_subscriptions');
        Schema::dropIfExists('notification_types');
    }
};
