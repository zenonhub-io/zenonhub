<?php

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
        Schema::table('nom_tokens', function (Blueprint $table) {
            $table->string('total_supply')->default(0)->change();
        });

        Schema::table('nom_account_blocks', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_account_tokens', function (Blueprint $table) {
            $table->string('balance')->default(0)->change();
        });

        Schema::table('nom_account_rewards', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_token_burns', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_token_mints', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_fusions', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });

        Schema::table('nom_stakes', function (Blueprint $table) {
            $table->string('amount')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nom_tokens', function (Blueprint $table) {
            $table->bigInteger('total_supply')->default(0)->change();
        });

        Schema::table('nom_account_blocks', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('nom_account_tokens', function (Blueprint $table) {
            $table->bigInteger('balance')->default(0)->change();
        });

        Schema::table('nom_account_rewards', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('nom_token_burns', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('nom_token_mints', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('nom_fusions', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });

        Schema::table('nom_stakes', function (Blueprint $table) {
            $table->bigInteger('amount')->default(0)->change();
        });
    }
};
