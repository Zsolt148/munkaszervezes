<?php

use App\Http\Controllers\Tasks\HistoryController;
use App\Http\Controllers\Tasks\LeaveController;
use App\Http\Controllers\Tasks\TaskCommentController;
use App\Http\Controllers\Tasks\TaskController;
use App\Http\Controllers\Tasks\TaskExportController;
use App\Http\Controllers\Tasks\TaskFetchController;
use App\Http\Controllers\Tasks\TaskGeneratorController;
use App\Http\Controllers\Tasks\TaskKanbanController;
use App\Http\Controllers\Tasks\TaskLogTimeController;
use App\Http\Controllers\Tasks\TaskPropsController;
use App\Http\Controllers\Tasks\TaskSearchController;
use App\Http\Controllers\Tasks\TaskTabController;
use App\Http\Controllers\Tasks\TaskWatchController;
use Illuminate\Support\Facades\Route;

//Route::get('tempo', [TaskController::class, 'getTempoPage'])->name('tempo');

// Axios fetch
Route::get('get-responsibles/{id?}', [TaskFetchController::class, 'getResponsibles'])->name('get-responsibles');
Route::get('get-task-statuses/{id}', [TaskFetchController::class, 'getTaskStatuses'])->name('get-task-statuses');
Route::get('get-working-hours/{id}', [TaskFetchController::class, 'getWorkingHours'])->name('get-working-hours');
Route::get('get-watchers/{id}', [TaskFetchController::class, 'getWatchers'])->name('get-watchers');
Route::get('get-comments/{id}', [TaskFetchController::class, 'getComments'])->name('get-comments');
Route::get('get-available', [TaskFetchController::class, 'getAvailableTasks'])->name('get-available-data');
Route::get('tempo/get-data', [TaskFetchController::class, 'getTempoData'])->name('get-tempo-data');

Route::post('{taskId}/log-time', [TaskLogTimeController::class, 'store'])->name('log-time');
Route::post('{taskId}/update-log-time', [TaskLogTimeController::class, 'update'])->name('update-log-time');
Route::delete('task-log-time/{taskWorkLogId}/delete', [TaskLogTimeController::class, 'destroy'])->name('task-work-log.delete');

Route::post('watch-task/{taskId}', [TaskWatchController::class, 'watchTask'])->name('watch-task');
Route::post('unwatch-task/{taskId}', [TaskWatchController::class, 'unWatchTask'])->name('unwatch-task');

// Tasks tab axios requests
Route::get('tab/all', [TaskTabController::class, 'all'])->name('tab.all');
Route::get('tab/done', [TaskTabController::class, 'done'])->name('tab.done');
Route::get('tab/my', [TaskTabController::class, 'my'])->name('tab.my');
Route::get('tab/leaves', [TaskTabController::class, 'leaves'])->name('tab.leaves');

// Task comments
Route::post('{task}/comment/store', [TaskCommentController::class, 'store'])->withTrashed()->name('store-comment');
Route::put('comments/{comment}/update', [TaskCommentController::class, 'update'])->withTrashed()->name('update-comment');
Route::delete('comments/{comment}/delete', [TaskCommentController::class, 'destroy'])->withTrashed()->name('delete-comment');

// Props for tasks index, create, edit..
Route::get('props', TaskPropsController::class)->name('props');

// Story - subtasks
Route::get('search/stories', [TaskSearchController::class, 'stories'])->name('search.stories');
Route::get('search/subtasks', [TaskSearchController::class, 'subtasks'])->name('search.subtasks');

// Kanban
Route::get('kanban/fetch', [TaskKanbanController::class, 'fetch'])->name('kanban.fetch');
Route::patch('kanban/{task}/update', [TaskKanbanController::class, 'update'])->name('kanban.update');

// Leaves, sick days..
Route::patch('leave/{leave}/accept', [LeaveController::class, 'accept'])->name('leave.accept');
Route::patch('leave/{leave}/decline', [LeaveController::class, 'decline'])->name('leave.decline');
Route::resource('leave', LeaveController::class)->only('store', 'update', 'destroy');

Route::get('export', TaskExportController::class)->name('export');

// CRUD
Route::get('/', [TaskController::class, 'index'])->name('index');
Route::get('create', [TaskController::class, 'create'])->name('create');
Route::post('store', [TaskController::class, 'store'])->name('store');
Route::get('{task}', [TaskController::class, 'show'])->withTrashed()->name('show');
Route::get('{task}/edit', [TaskController::class, 'edit'])->withTrashed()->name('edit');
Route::patch('{task}/update', [TaskController::class, 'update'])->withTrashed()->name('update');
Route::delete('{task}/destroy', [TaskController::class, 'destroy'])->withTrashed()->name('destroy');
Route::patch('{task}/restore', [TaskController::class, 'restore'])->withTrashed()->name('restore');
Route::delete('{task}/force-delete', [TaskController::class, 'forceDelete'])->withTrashed()->name('force-delete');

// History
Route::get('{task}/history', HistoryController::class)->name('history');

// Generator
Route::get('task-generator/fetch', [TaskGeneratorController::class, 'fetchTasks'])->name('task-generator.fetch-tasks');
Route::post('task-generator/store', [TaskGeneratorController::class, 'createTasks'])->name('task-generator.create-tasks');
