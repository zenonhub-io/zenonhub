<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('nom:reset-db', function () {
    Artisan::call('migrate:rollback');
    Artisan::call('migrate');
    Artisan::call('db:seed --class=DatabaseSeeder');
    Artisan::call('db:seed --class=GenesisSeeder');
})->purpose('Delete recent users')->daily();
