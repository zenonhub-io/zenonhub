<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nom_stakes', function (Blueprint $table) {
            $table->foreignId('token_id')
                ->nullable()
                ->after('account_id')
                ->references('id')
                ->on('nom_tokens')
                ->cascadeOnDelete();
        });

    }

    public function down(): void
    {
        Schema::table('nom_stakes', function (Blueprint $table) {
            $table->dropForeign(['token_id']);
        });
    }
};
