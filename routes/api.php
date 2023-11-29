<?php

use App\Http\Controllers\Api\CitiesController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('dashboard/get-data', [DashboardController::class, 'fetch'])->name('dashboard.fetch');

Route::get('cities/{zip}', [CitiesController::class, 'getCities'])->name('citites.get-cities');
