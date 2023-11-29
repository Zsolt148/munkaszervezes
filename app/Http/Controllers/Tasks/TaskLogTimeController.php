<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskWorkingHourRequest;
use App\Models\Task;
use App\Models\TaskWorkingHour;
use Illuminate\Http\Request;

class TaskLogTimeController extends Controller
{
    public function store(TaskWorkingHourRequest $request, $taskId)
    {
        $task = Task::withTrashed()->findOrFail($taskId);

        $this->authorize('update', $task);

        $task->workingHours()->create([
            'time' => $request->time,
            'admin_id' => $this->user()->id,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', trans('Time recorded'));
    }

    public function update(TaskWorkingHourRequest $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        $this->authorize('update', $task);

        $logTime = $task->workingHours()->where('id', $request->get('id'))->first();

        $logTime->time = $request->get('time');
        $logTime->date = $request->get('date');
        $logTime->description = $request->get('description');

        $logTime->save();

        return redirect()->back()->with('success', trans('Successfully updated'));
    }

    public function destroy(Request $request, $taskWorkLogId)
    {
        $taskWorkLog = TaskWorkingHour::findOrFail($taskWorkLogId);

        $this->authorize('update', $taskWorkLog->task);

        $taskWorkLog->delete();

        return redirect()->back()->with('success', trans('Successfully deleted'));
    }
}
