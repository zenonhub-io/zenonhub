<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nom_momentums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chain_id')->references('id')->on('nom_chains');
            $table->foreignId('producer_account_id')->references('id')->on('nom_accounts');
            $table->foreignId('producer_pillar_id')->nullable()->references('id')->on('nom_pillars');
            $table->integer('version')->default(1);
            $table->bigInteger('height')->unique();
            $table->string('hash')->unique();
            $table->text('data')->nullable();
            $table->timestamp('created_at');
        });

        Artisan::call('nom:create-or-update-latest-momentums-view');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS view_latest_nom_momentums');
        Schema::dropIfExists('nom_momentums');
    }
};
