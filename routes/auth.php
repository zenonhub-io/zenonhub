<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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
