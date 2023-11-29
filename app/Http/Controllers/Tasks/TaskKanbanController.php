<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TaskKanbanController extends Controller
{
    public function fetch(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->withoutEagerLoads()
            ->with('role', 'responsible', 'park')
            ->forUser($this->user())
//            ->whereNot('responsible_id', null) // Azért tettem bele, mert a feladatoknál a filterezés errort dob, ha nincs responsible_id
            ->day($request->get('day'))
            ->week($request->get('week', []))
            ->month($request->get('month', []))
            ->get()
            ->filter(function (Task $task) {
                // Ebbe a státuszba kerülés után az oda húzott ticketek alapértelmezetten 48 órán belül eltűnnek
                if ($task->done_at) {
                    return $task->done_at->diffInHours(now()->subHours(48));
                }

                return true;
            });

        return response()->json([
            'tasks' => TaskResource::collection($tasks),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(array_keys(Task::STATUSES))],
        ]);

        $status = $validated['status'];

        if ($status == $task->status) {
            return response()->json();
        }

        $task->status = $status;
        $task->status_changed_at = now();
        $task->save();

        return response()->json([
            'success' => 'Feladat státusz frissítve',
            'statusText' => trans('tasks.'.$status),
        ]);
    }
}
