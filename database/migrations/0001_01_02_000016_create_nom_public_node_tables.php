<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nom_public_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('ip')->unique();
            $table->string('version')->nullable();
            $table->string('isp')->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('country')->nullable();
            $table->string('country_code')->nullable();
            $table->decimal('latitude', 9, 6)->nullable();
            $table->decimal('longitude', 9, 6)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('discovered_at')->nullable();
        });

        Schema::create('nom_public_node_histories', function (Blueprint $table) {
            $table->id();
            $table->integer('node_count');
            $table->integer('unique_versions');
            $table->integer('unique_isps');
            $table->integer('unique_cities');
            $table->integer('unique_countries');
            $table->date('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nom_public_nodes');
        Schema::dropIfExists('nom_public_node_histories');
    }
};
