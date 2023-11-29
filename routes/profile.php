<?php

use App\Helpers\AdminFeatures;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\ConfirmedPasswordStatusController;
use App\Http\Controllers\User\OtherBrowserSessionsController;
use App\Http\Controllers\User\ProfileDateFormatController;
use App\Http\Controllers\User\ProfileInformationController;
use App\Http\Controllers\User\ProfileNotificationController;
use App\Http\Controllers\User\ProfilePhotoController;
use App\Http\Controllers\User\UserDeletionController;
use App\Http\Controllers\User\UserPasswordController;
use App\Http\Controllers\User\UserProfileController;
use App\Http\Controllers\User\UserTempoController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;

Route::get('/', [UserProfileController::class, 'show'])
    ->name('profile.show');

Route::get('/tempo', UserTempoController::class)->name('profile.tempo');

// Refactor to another controller maybe
Route::get('tab/leaves', [UserProfileController::class, 'leaves'])->name('profile.leaves');

Route::put('/profile-information', [ProfileInformationController::class, 'update'])
    ->name('user-profile-information.update');

Route::put('/profile-notification', [ProfileNotificationController::class, 'update'])
    ->name('user-notification.update');

Route::put('/profile-date-time-format', [ProfileDateFormatController::class, 'update'])
    ->name('user-profile-date-time-format.update');

Route::post('/other-browser-sessions', [OtherBrowserSessionsController::class, 'destroy'])
    ->name('other-browser-sessions.destroy');

Route::delete('/profile-photo', [ProfilePhotoController::class, 'destroy'])
    ->name('current-user-photo.destroy');

Route::get('/confirmed-password-status', [ConfirmedPasswordStatusController::class, 'show'])
    ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
    ->name('password.confi');

Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
    ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
    ->name('confirm.password');

if (AdminFeatures::canDeleteAccount()) {
    Route::delete('/delete', UserDeletionController::class)
        ->name('current-user.destroy');
}

if (AdminFeatures::canUpdatePasswords()) {
    Route::put('/password', UserPasswordController::class)
        ->middleware([config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')])
        ->name('user-password.update');
}

// Two Factor Authentication...
if (AdminFeatures::canManageTwoFactorAuthentication()) {

    $twoFactorMiddleware = AdminFeatures::optionEnabled(AdminFeatures::twoFactorAuthentication(), 'confirmPassword')
        ? [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard'), 'password.confirm']
        : [config('fortify.auth_middleware', 'auth').':'.config('fortify.guard')];

    Route::post('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.enable');

    Route::post('/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.confirm');

    Route::delete('/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.disable');

    Route::get('/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.qr-code');

    Route::get('/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.secret-key');

    Route::get('/two-factor-recovery-codes', [RecoveryCodeController::class, 'index'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.recovery-codes');

    Route::post('/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        ->middleware($twoFactorMiddleware)
        ->name('two-factor.recovery-codes.submit');
}
