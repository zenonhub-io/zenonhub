<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::redirect('nodes', '/services/public-nodes', 301);
Route::redirect('tools/node-map', '/stats/public-nodes', 301);
Route::redirect('tools/node-statistics', '/stats/public-nodes', 301);
Route::redirect('tools/api', '/tools/api-playground', 301);
Route::redirect('sign-up', '/register', 301);
Route::redirect('sign-in', '/login', 301);
