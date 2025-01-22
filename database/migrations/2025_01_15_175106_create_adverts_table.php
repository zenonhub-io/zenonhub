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
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->references('id')->on('users');
            $table->foreignId('account_block_id')->nullable()->references('id')->on('nom_account_blocks');
            $table->string('headline');
            $table->string('body');
            $table->string('cta_text');
            $table->string('cta_link');
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_contact')->nullable();
            $table->string('placement')->default('sidebar');
            $table->integer('display_order')->default(0);
            $table->integer('display_count')->default(0);
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_confirmed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sponsors');
    }
};
