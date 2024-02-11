<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! app()->runningUnitTests()) {
            DB::statement('
CREATE OR REPLACE VIEW view_latest_nom_momentums AS
SELECT *
FROM nom_momentums
ORDER BY id DESC
LIMIT 50000;
');

            DB::statement('
CREATE OR REPLACE VIEW view_latest_nom_account_blocks AS
SELECT *
FROM nom_account_blocks
WHERE to_account_id != 1 AND (contract_method_id NOT IN (36, 68) OR contract_method_id IS null)
ORDER BY id DESC
LIMIT 50000;
');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! app()->runningUnitTests()) {
            DB::statement('DROP VIEW IF EXISTS view_latest_nom_account_blocks');
            DB::statement('DROP VIEW IF EXISTS view_latest_nom_momentums');
        }
    }
};
