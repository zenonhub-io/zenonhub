<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::redirect('nodes', '/services/public-nodes', 301);
Route::redirect('tools/node-map', '/stats/public-nodes', 301);
Route::redirect('tools/node-statistics', '/stats/public-nodes', 301);
Route::redirect('tools/api', '/tools/api-playground', 301);
Route::redirect('sign-up', '/register', 301);
Route::redirect('sign-in', '/login', 301);

Route::get('explorer/transactions', function () {
    $query = request()->getQueryString();

    return redirect('/explorer/blocks' . ($query ? "?{$query}" : ''), 301);
});

Route::get('explorer/transaction/{path}', function (string $path) {
    $query = request()->getQueryString();

    return redirect("/explorer/block/{$path}" . ($query ? "?{$query}" : ''), 301);
})->where('path', '.*');

Route::get('explorer/account/{address}/transactions', function (string $address) {
    $query = request()->getQueryString();

    return redirect("/explorer/account/{$address}/blocks" . ($query ? "?{$query}" : ''), 301);
});

Route::get('explorer/momentum/{hash}/transactions', function (string $hash) {
    $query = request()->getQueryString();

    return redirect("/explorer/account/{$hash}/blocks" . ($query ? "?{$query}" : ''), 301);
});
