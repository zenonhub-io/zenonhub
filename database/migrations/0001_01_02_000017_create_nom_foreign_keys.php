<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nom_contracts', function (Blueprint $table) {
            $table->foreignId('account_id')->after('chain_id')->nullable()->references('id')->on('nom_accounts');
        });

        Schema::table('nom_account_rewards', function ($table) {
            $table->foreignId('account_block_id')->after('chain_id')->nullable()->references('id')->on('nom_account_blocks');
        });

        Schema::table('nom_votes', function (Blueprint $table) {
            $table->after('id', function ($table) {
                $table->foreignId('owner_id')->nullable()->references('id')->on('nom_accounts');
                $table->foreignId('pillar_id')->nullable()->references('id')->on('nom_pillars');
            });
        });
    }

    public function down(): void
    {
        Schema::table('nom_votes', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('nom_votes', function (Blueprint $table) {
            $table->dropForeign(['pillar_id']);
        });

        Schema::table('nom_account_rewards', function ($table) {
            $table->dropForeign(['account_block_id']);
        });

        Schema::table('nom_contracts', function (Blueprint $table) {
            $table->dropForeign(['account_id']);
        });
    }
};
