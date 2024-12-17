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
        Schema::create('nom_network_stat_histories', function (Blueprint $table) {
            $table->id();
            $table->string('total_tx')->default(0);
            $table->string('total_daily_tx')->default(0);
            $table->string('total_addresses')->default(0);
            $table->string('total_daily_addresses')->default(0);
            $table->string('total_active_addresses')->default(0);
            $table->string('total_tokens')->default(0);
            $table->string('total_daily_tokens')->default(0);
            $table->string('total_stakes')->default(0);
            $table->string('total_daily_stakes')->default(0);
            $table->string('total_staked')->default(0);
            $table->string('total_daily_staked')->default(0);
            $table->string('total_fusions')->default(0);
            $table->string('total_daily_fusions')->default(0);
            $table->string('total_fused')->default(0);
            $table->string('total_daily_fused')->default(0);
            $table->string('total_pillars')->default(0);
            $table->string('total_sentinels')->default(0);
            $table->date('date')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nom_network_stats');
    }
};
