<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\TaskCommentResource;
use App\Http\Resources\TaskResource;
use App\Http\Resources\TaskStatusResource;
use App\Http\Resources\TaskWorkingHourResource;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskWorkingHour;
use Illuminate\Http\Request;

class TaskFetchController extends Controller
{
    /*
     * Responsibles by a single id or an array of ids
     */
    public function getResponsibles(Request $request, $id = null)
    {
        $this->authorize('viewAny', Task::class);

        if ($id) {
            $roles = Role::find($id);
        } else {
            $roles = $request->get('roles');
        }

        $admins = Admin::role($roles ?? [])->get();

        return AdminResource::collection($admins);
    }

    public function getTaskStatuses($id)
    {
        $this->authorize('viewAny', Task::class);

        $task = Task::query()
            ->withoutEagerLoads()
            ->withTrashed()
            ->with('taskStatuses')
            ->find($id);

        if (! $task) {
            return [];
        }

        return TaskStatusResource::collection($task->taskStatuses);
    }

    public function getWorkingHours($id)
    {
        $this->authorize('viewAny', Task::class);

        $task = Task::query()
            ->withoutEagerLoads()
            ->withTrashed()
            ->with('workingHours')
            ->find($id);

        if (! $task) {
            return [];
        }

        return TaskWorkingHourResource::collection($task->workingHours);
    }

    public function getWatchers($id)
    {
        $this->authorize('viewAny', Task::class);

        $task = Task::query()->withTrashed()->with('watchers')->findOrFail($id);

        return response()->json($task->watchers);
    }

    public function getComments(Request $request, $id)
    {
        $this->authorize('viewAny', Task::class);

        $perPage = $request->input('per_page') ?? 5;
        $page = $request->input('page') ?? 1;

        $task = Task::withTrashed()->with('comments')->findOrFail($id)->fresh();

        $taskComments = $task->comments()->orderByDesc('created_at')->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => TaskCommentResource::collection($taskComments),
            'total' => $taskComments->lastPage(),
        ]);
    }

    public function getTempoData(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $taskWorkingHours = TaskWorkingHour::query()->with(['admin', 'task'])->search($request)->orderBy('date', 'ASC');

        return TaskWorkingHourResource::collection($taskWorkingHours->get());
    }

    public function getAvailableTasks(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->withTrashed()
            ->with(['tags', 'comments'])
            ->searchByUser($request)
            ->get();

        return TaskResource::collection($tasks);
    }
}
