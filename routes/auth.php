<?php

use Illuminate\Support\Facades\Route;

//
// Auth routes
Route::group(['middleware' => ['throttle:30,1']], function () {
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

    Route::get('auth/forgot-password', [\App\Http\Controllers\Auth\ForgotPassword::class, 'show'])
        ->middleware('guest')
        ->name('password.request');

    Route::post('auth/forgot-password', [\App\Http\Controllers\Auth\ForgotPassword::class, 'store'])
        ->middleware('guest');

    Route::get('auth/reset-password/{token}', [\App\Http\Controllers\Auth\ResetPassword::class, 'show'])
        ->middleware('guest')
        ->name('password.reset');

    Route::post('auth/reset-password', [\App\Http\Controllers\Auth\ResetPassword::class, 'store'])
        ->middleware('guest')
        ->name('password.update');

    Route::post('auth/resend-verification', [\App\Http\Controllers\Auth\SendVerification::class, 'store'])
        ->middleware('auth')
        ->name('verification.send');

    Route::get('auth/verify-email', [\App\Http\Controllers\Auth\VerifyEmail::class, 'show'])
        ->middleware('auth')
        ->name('verification.notice');

    Route::get('auth/verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmail::class, 'store'])
        ->middleware('auth')
        ->name('verification.verify');
});
