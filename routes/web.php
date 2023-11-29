<?php

use App\Helpers\AdminFeatures;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProxyController;
use App\Http\Controllers\AdminSearchController;
use App\Http\Controllers\Api\AppSiteSearchController;
use App\Http\Controllers\Api\LogTableController;
use App\Http\Controllers\Api\TagSearchController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\LanguageSwitchController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PlanVariantController;
use App\Http\Controllers\Tasks\TaskResourcePlanningController;
use Illuminate\Support\Facades\Route;

Route::get('app/search', AppSiteSearchController::class)->name('app.search');

Route::redirect('/', '/dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('language/{language}', LanguageSwitchController::class)->name('language');

Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::get('notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
Route::get('notifications/fetch-all', [NotificationController::class, 'fetchAll'])->name('notifications.fetch-all');
Route::get('notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
Route::post('notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
Route::post('notifications/{id}/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
Route::delete('notifications/{id}/delete', [NotificationController::class, 'delete'])->name('notifications.delete');

Route::post('admins/{admin}/reinvite', [AdminController::class, 'reinvite'])->withTrashed()->name('admins.reinvite');
Route::post('admins/{admin}/unblock', [AdminController::class, 'unblock'])->withTrashed()->name('admins.unblock');
Route::patch('admins/{admin}/restore', [AdminController::class, 'restore'])->withTrashed()->name('admins.restore');
Route::delete('admins/{admin}/force-delete', [AdminController::class, 'forceDelete'])->withTrashed()->name('admins.force-delete');
Route::delete('admins/{admin}/delete-photo', [AdminController::class, 'deletePhoto'])->withTrashed()->name('admins.deletePhoto');
Route::resource('admins', AdminController::class)->withTrashed();

// Resource planning
Route::get('resource-planning/fetch/group', [TaskResourcePlanningController::class, 'fetchGroup'])->name('resource-planning.fetch-group');
Route::get('resource-planning/fetch/workers', [TaskResourcePlanningController::class, 'fetchWorkers'])->name('resource-planning.fetch-workers');
Route::patch('resource-planning/{task}/update', [TaskResourcePlanningController::class, 'update'])->name('resource-planning.update');
Route::patch('resource-planning/{task}/remove', [TaskResourcePlanningController::class, 'remove'])->name('resource-planning.remove');
Route::resource('resource-planning', TaskResourcePlanningController::class)->only('index');

// Pre planning
Route::get('plan-variant/import', [PlanVariantController::class, 'importCurrentPlan'])->name('plan-variants.import-current');
Route::post('plan-variant/store', [PlanVariantController::class, 'storePlanTask'])->name('plan-variants.store');
Route::patch('plan-variant/{variant}/update', [PlanVariantController::class, 'update'])->name('plan-variants.update');
Route::patch('plan-variant/{task}/{variant}/remove', [PlanVariantController::class, 'removePlanTask'])->name('plan-variants.remove');
Route::patch('plan-variant/{variant}/override', [PlanVariantController::class, 'override'])->name('plan-variants.override');

// Fetches only the tasks for the shared pool
Route::get('plan-variant/fetch/variants', [PlanVariantController::class, 'fetchVariants'])->name('plan-variants.fetch-variants');
Route::get('plan-variant/fetch/tasks', [PlanVariantController::class, 'fetchTasks'])->name('plan-variants.fetch-tasks');
Route::get('plan-variant/fetch/data', [PlanVariantController::class, 'fetchData'])->name('plan-variants.fetch-data');
Route::resource('plan-variant', PlanVariantController::class)->only('store', 'update', 'destroy');

// Tasks
Route::prefix('tasks')
    ->name('tasks.')
    ->group(base_path('routes/tasks.php'));

// Simple file upload
Route::post('file-upload', FileUploadController::class)
    ->withoutMiddleware('throttle:auth')
    ->middleware('throttle:none')
    ->name('file-upload');

// Logs
if (AdminFeatures::hasLogs()) {
    Route::get('logs/table', LogTableController::class)->name('logs.table');
    Route::resource('logs', LogController::class)->only('index');
}

// Settings
Route::prefix('settings')
    ->name('settings.')
    ->group(base_path('routes/settings.php'));

// Impersonation
Route::get('/admin-proxy-enter/{id}', [AdminProxyController::class, 'enter'])->name('admin-proxy.enter');
Route::get('/admin-proxy-exit', [AdminProxyController::class, 'exit'])->name('admin-proxy.exit');

// Admins search combobox
Route::get('admins-search', AdminSearchController::class)->name('admins.search');

// Tags search - TagCombobox.vue
Route::get('/tags/search/{locale}/{type?}', TagSearchController::class)->name('tags.search');
