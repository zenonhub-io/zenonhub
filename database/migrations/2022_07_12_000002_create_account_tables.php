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
        Schema::create('nom_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('address')->unique();
            $table->string('public_key')->nullable();
            $table->string('name')->index()->nullable();
            $table->bigInteger('znn_balance')->index()->default(0);
            $table->bigInteger('qsr_balance')->index()->default(0);
            $table->bigInteger('znn_staked')->index()->default(0);
            $table->bigInteger('qsr_fused')->index()->default(0);
            $table->bigInteger('znn_locked')->index()->default(0);
            $table->bigInteger('qsr_locked')->index()->default(0);
            $table->bigInteger('total_znn_balance')->index()->default(0);
            $table->bigInteger('total_qsr_balance')->index()->default(0);
            $table->bigInteger('total_znn_rewards')->index()->default(0);
            $table->bigInteger('total_qsr_rewards')->index()->default(0);
            $table->bigInteger('total_znn_sent')->index()->default(0);
            $table->bigInteger('total_znn_received')->index()->default(0);
            $table->bigInteger('total_qsr_sent')->index()->default(0);
            $table->bigInteger('total_qsr_received')->index()->default(0);
            $table->boolean('is_embedded_contract')->default(0);
            $table->timestamp('updated_at')->index()->nullable();
        });

        Artisan::call('db:seed', array('--class' => 'AccountsSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nom_accounts');
    }
};
