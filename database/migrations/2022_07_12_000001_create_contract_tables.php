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
        Schema::create('nom_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
        });

        Schema::create('nom_contract_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->references('id')->on('nom_contracts')->cascadeOnDelete();
            $table->string('name')->index();
            $table->string('signature');
            $table->string('fingerprint')->index();
        });

        Artisan::call('db:seed', array('--class' => 'ContractMethodSeeder'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('nom_contract_methods');
        Schema::dropIfExists('nom_contracts');
        Schema::enableForeignKeyConstraints();
    }
};
