<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect Routes
|--------------------------------------------------------------------------
|
| Here is where you can register redirects for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::redirect('tools/node-map', '/tools/node-statistics', 301);
Route::redirect('tools/api', '/tools/api-playground', 301);
Route::redirect('tools/node-statistics', '/stats/nodes', 301);
