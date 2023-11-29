<?php

use App\Http\Controllers\DevopsController;
use Illuminate\Support\Facades\Route;

Route::get('horizon', [DevopsController::class, 'horizon'])->name('devops.horizon');
Route::get('telescope', [DevopsController::class, 'telescope'])->name('devops.telescope');
