
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\StaffPasswordRecoveryController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    // Employee login
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Employee password recovery via OTP
    Route::get('forgot-password', [StaffPasswordRecoveryController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [StaffPasswordRecoveryController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('forgot-password/verify', [StaffPasswordRecoveryController::class, 'showVerify'])
        ->name('password.verify');

    Route::post('forgot-password/verify', [StaffPasswordRecoveryController::class, 'verify'])
        ->middleware('throttle:10,1')
        ->name('password.verify.submit');

    Route::post('forgot-password/resend', [StaffPasswordRecoveryController::class, 'resend'])
        ->middleware('throttle:5,1')
        ->name('password.resend');

    Route::get('reset-password', [StaffPasswordRecoveryController::class, 'showReset'])
        ->name('password.reset');

    Route::post('reset-password', [StaffPasswordRecoveryController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

Route::middleware('auth')->group(function () {

    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
