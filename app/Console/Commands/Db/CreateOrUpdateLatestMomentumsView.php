<?php

declare(strict_types=1);

namespace App\Console\Commands\Db;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateOrUpdateLatestMomentumsView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-or-update-latest-momentums-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update latest momentums view';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::statement('DROP VIEW IF EXISTS view_latest_nom_momentums');
        DB::statement('
            CREATE VIEW view_latest_nom_momentums AS
                SELECT *
                FROM nom_momentums
                ORDER BY id DESC
                LIMIT 50000
        ');
    }
}
