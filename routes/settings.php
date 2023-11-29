<?php

use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/get-primary-color', [SettingsController::class, 'getPrimaryColor'])->name('getPrimaryColor');
Route::get('/', [SettingsController::class, 'index'])->name('index');
Route::put('/save-socials', [SettingsController::class, 'saveSocials'])->name('saveSocials');
Route::put('/save-color', [SettingsController::class, 'saveColor'])->name('saveColor');
Route::post('/save-slogan', [SettingsController::class, 'saveSlogan'])->name('saveSlogan');
Route::post('/save-description', [SettingsController::class, 'saveDescription'])->name('saveDescription');
Route::put('/save-contact', [SettingsController::class, 'saveContact'])->name('saveContact');
Route::put('/save-seo', [SettingsController::class, 'saveSeo'])->name('saveSeo');
Route::put('/save-seo-codes', [SettingsController::class, 'saveSeoCodes'])->name('saveSeoCodes');
Route::post('/upload-logo', [SettingsController::class, 'uploadLogo'])->name('uploadLogo');
Route::post('/upload-favicon', [SettingsController::class, 'uploadFavicon'])->name('uploadFavicon');
Route::post('/maintenance', [SettingsController::class, 'saveMaintenanceMode'])->name('saveMaintenanceMode');
Route::post('/security', [SettingsController::class, 'saveSecurity'])->name('saveSecurity');
Route::post('/create-policy', [SettingsController::class, 'createPolicy'])->name('createPolicy');
Route::put('/update-policy/{policy}', [SettingsController::class, 'updatePolicy'])->name('updatePolicy');
Route::delete('/delete-policy/{policy}', [SettingsController::class, 'deletePolicy'])->name('deletePolicy');
