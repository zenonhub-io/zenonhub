<?php

use Illuminate\Support\Facades\Route;

Route::redirect('nodes', '/services/public-nodes', 301);
Route::redirect('tools/node-map', '/stats/nodes', 301);
Route::redirect('tools/node-statistics', '/stats/nodes', 301);
Route::redirect('tools/api', '/tools/api-playground', 301);
