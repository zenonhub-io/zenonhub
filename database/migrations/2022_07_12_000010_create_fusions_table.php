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
        Schema::create('nom_fusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->foreignId('to_account_id')->nullable()->references('id')->on('nom_accounts')->nullOnDelete();
            $table->bigInteger('amount')->index();
            $table->string('hash')->nullable();
            $table->timestamp('started_at')->index()->nullable();
            $table->timestamp('ended_at')->index()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nom_fusions');
    }
};
