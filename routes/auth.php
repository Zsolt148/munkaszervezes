<?php

use App\Helpers\AdminFeatures;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredAdminController;
use App\Http\Controllers\Auth\TwoFactorAuthenticatedSessionController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest:admin')->group(function () {

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->name('login.submit');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');

    if (AdminFeatures::canRegister()) {
        Route::get('register', [RegisteredAdminController::class, 'create'])
            ->name('register');

        Route::post('register/submit', [RegisteredAdminController::class, 'store'])
            ->name('register.submit');
    }

    if (AdminFeatures::canManageTwoFactorAuthentication()) {

        $twoFactorLimiter = config('fortify.limiters.two-factor');

        Route::get('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'create'])
            ->name('two-factor.login');

        Route::post('/two-factor-challenge', [TwoFactorAuthenticatedSessionController::class, 'store'])
            ->middleware(array_filter([
                $twoFactorLimiter ? 'throttle:'.$twoFactorLimiter : null,
            ]))
            ->name('two-factor.submit');
    }
});

Route::middleware('auth:admin')->group(function () {

    if (AdminFeatures::hasEmailVerification()) {
        Route::get('verify-email', [EmailVerificationPromptController::class, '__invoke'])
            ->middleware('throttle:6,1')
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');
    }

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->name('confirm-password');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
