<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//
// Sitemap
Route::get('/sitemap.xml', function () {
    $file = storage_path('app/sitemap/sitemap.xml');
    return response()->file($file, [
        'Content-Type' => "application/xml",
    ]);
})->name('sitemap');

//
// Pages

Route::get('/', [\App\Http\Controllers\Site\Home::class, 'show'])->name('home');

Route::prefix('pillars')->name('pillars.')->middleware([])->group(function () {
    Route::get('/', [\App\Http\Controllers\Pillar\Pillars::class, 'show'])->name('overview');
    Route::get('/{slug}', [\App\Http\Controllers\Pillar\Pillars::class, 'detail'])->name('detail');
});

Route::prefix('accelerator-z')->name('az.')->middleware([])->group(function () {
    Route::get('/', [\App\Http\Controllers\Accelerator\Projects::class, 'show'])->name('overview');
    Route::get('project/{hash}', [\App\Http\Controllers\Accelerator\Projects::class, 'detail'])->name('project');
    Route::get('phase/{hash}', [\App\Http\Controllers\Accelerator\Phases::class, 'detail'])->name('phase');
});

Route::prefix('explorer')->name('explorer.')->middleware([])->group(function () {
    Route::get('/', [\App\Http\Controllers\Explorer\Explorer::class, 'show'])->name('overview');

    Route::get('momentums/', [\App\Http\Controllers\Explorer\Momentums::class, 'show'])->name('momentums');
    Route::get('momentum/{hash}', [\App\Http\Controllers\Explorer\Momentums::class, 'detail'])->name('momentum');

    Route::get('transactions/', [\App\Http\Controllers\Explorer\Transactions::class, 'show'])->name('transactions');
    Route::get('transaction/{hash}', [\App\Http\Controllers\Explorer\Transactions::class, 'detail'])->name('transaction');

    Route::get('accounts/', [\App\Http\Controllers\Explorer\Accounts::class, 'show'])->name('accounts');
    Route::get('account/{address}', [\App\Http\Controllers\Explorer\Accounts::class, 'detail'])->name('account');

    Route::get('tokens/', [\App\Http\Controllers\Explorer\Tokens::class, 'show'])->name('tokens');
    Route::get('token/{zts}', [\App\Http\Controllers\Explorer\Tokens::class, 'detail'])->name('token');

    Route::get('staking/', [\App\Http\Controllers\Explorer\Staking::class, 'show'])->name('staking');

    Route::get('fusions/', [\App\Http\Controllers\Explorer\Fusions::class, 'show'])->name('fusions');
});

Route::prefix('tools')->name('tools.')->middleware(['throttle:60,1'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Tools\Overview::class, 'show'])->name('overview');
    Route::get('api-playground', [\App\Http\Controllers\Tools\ApiPlayground::class, 'show'])->name('api-playground');
    Route::get('verify-signature', [\App\Http\Controllers\Tools\VerifySignature::class, 'show'])->name('verify-signature');
    Route::get('broadcast-message', [\App\Http\Controllers\Tools\BroadcastMessage::class, 'show'])->name('broadcast-message');
    Route::get('node-statistics', [\App\Http\Controllers\Tools\NodeStatistics::class, 'show'])->name('node-statistics');
});

Route::prefix('account')->name('account.')->middleware(['throttle:60,1', 'auth', 'verified'])->group(function () {

    Route::get('/', [\App\Http\Controllers\Account\Overview::class, 'show'])->name('overview');

    Route::get('details', [\App\Http\Controllers\Account\Details::class, 'show'])->name('details');
    Route::post('details', [\App\Http\Controllers\Account\Details::class, 'store']);

    Route::get('addresses', [\App\Http\Controllers\Account\Addresses::class, 'show'])->name('addresses');
    Route::post('addresses', [\App\Http\Controllers\Account\Addresses::class, 'store']);

    Route::get('security', [\App\Http\Controllers\Account\Security::class, 'show'])->name('security');
    Route::post('security', [\App\Http\Controllers\Account\Security::class, 'store']);

    Route::get('notifications', [\App\Http\Controllers\Account\Notifications::class, 'show'])->name('notifications');
    Route::post('notifications', [\App\Http\Controllers\Account\Notifications::class, 'store']);
});

//
// Auth routes
Route::get('login', [\App\Http\Controllers\Auth\Login::class, 'show'])
    ->middleware('guest')
    ->name('login');

Route::post('login', [\App\Http\Controllers\Auth\Login::class, 'store'])
    ->middleware('guest');

Route::get('logout', [\App\Http\Controllers\Auth\Login::class, 'destroy'])
    ->middleware(['auth'])
    ->name('logout');

Route::get('sign-up', [\App\Http\Controllers\Auth\Signup::class, 'show'])
    ->middleware('guest')
    ->name('sign-up');

Route::post('sign-up', [\App\Http\Controllers\Auth\Signup::class, 'store'])
    ->middleware('guest');

Route::prefix('auth')->group(function () {
    Route::get('forgot-password', [\App\Http\Controllers\Auth\ForgotPassword::class, 'show'])
        ->middleware('guest')
        ->name('password.request');

    Route::post('forgot-password', [\App\Http\Controllers\Auth\ForgotPassword::class, 'store'])
        ->middleware('guest');

    Route::get('reset-password/{token}', [\App\Http\Controllers\Auth\ResetPassword::class, 'show'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('reset-password', [\App\Http\Controllers\Auth\ResetPassword::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    Route::post('resend-verification', [\App\Http\Controllers\Auth\SendVerification::class, 'store'])
        ->middleware('auth')
        ->name('verification.send');

    Route::get('verify-email', [\App\Http\Controllers\Auth\VerifyEmail::class, 'show'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmail::class, 'store'])
        ->middleware('auth')
        ->name('verification.verify');
});
