<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class TaskWatchController extends Controller
{
    public function watchTask($taskId)
    {
        $task = Task::query()
            ->with('watchers')
            ->findOrFail($taskId);

        $task->watchers()->attach(Auth::user()->id);

        return redirect()->back()->with('success', __('Successfully updated'));
    }

    public function unWatchTask($taskId)
    {
        $task = Task::query()
            ->with('watchers')
            ->findOrFail($taskId);

        $task->watchers()->detach(Auth::user()->id);

        return redirect()->back()->with('success', __('Successfully updated'));
    }
}
