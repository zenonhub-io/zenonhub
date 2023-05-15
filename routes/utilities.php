<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Utility Routes
|--------------------------------------------------------------------------
|
| Here is where you can register redirects for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

//
// Sitemap
Route::get('/sitemap.xml', function () {
    $file = storage_path('app/sitemap/sitemap.xml');

    return response()->file($file, [
        'Content-Type' => 'application/xml',
    ]);
})->name('sitemap');
