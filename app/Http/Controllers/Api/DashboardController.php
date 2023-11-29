<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Task;

class DashboardController extends Controller
{
    public function fetch()
    {
        $users = Admin::query()->withoutEagerLoads();
        $tasks = Task::query()->withoutEagerLoads();

        return response()->json([
            'sumOfUsers' => (clone $users)->count(),
            'registeredUsers' => (clone $users)->where('status', 'registered')->count(),
            'invitedUsers' => (clone $users)->where('status', 'invited')->count(),
            'blockedUsers' => (clone $users)->where('status', 'blocked')->count(),

            'sumOfTasks' => (clone $tasks)->count(),
            'todoTasks' => (clone $tasks)->whereStatus(Task::STATUS_TODO)->count(),
            'inprogressTasks' => (clone $tasks)->whereStatus(Task::STATUS_IN_PROGRESS)->count(),
            'waitingTasks' => (clone $tasks)->whereStatus(Task::STATUS_WAITING)->count(),
            'repairTasks' => (clone $tasks)->whereStatus(Task::STATUS_WAITING_FOR_REPAIR)->count(),
            'doneTasks' => (clone $tasks)->whereStatus(Task::STATUS_DONE)->count(),
        ]);
    }
}
