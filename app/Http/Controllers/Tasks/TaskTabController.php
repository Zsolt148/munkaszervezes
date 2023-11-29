<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\TaskResource;
use App\Models\Leave;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class TaskTabController extends Controller
{
    public function all(): JsonResponse
    {
        return response()->json();
    }

    public function done(): JsonResponse
    {
        $tasks = Task::query()
            ->with('role', 'tags', 'comments', 'responsible', 'taskStatuses', 'subtasks', 'createdBy', 'workingHours')
            ->whereNull('parent_id')
            ->forUser($this->user())
            ->withTrashed()
            ->done()
            ->get();

        return response()->json(
            array_merge(app(TaskService::class)->props(), [
                'tasks' => TaskResource::collection($tasks),
            ])
        );
    }

    public function my(): JsonResponse
    {
        $tasks = Task::query()
            ->with('role', 'tags', 'comments', 'responsible', 'taskStatuses', 'createdBy')
            ->responsibleId($this->user())
            ->withTrashed()
            ->get();

        return response()->json([
            'tasks' => TaskResource::collection($tasks),
        ]);
    }

    public function leaves(): JsonResponse
    {
        /** @var Collection $leaves */
        $leaves = Leave::query()
            ->with('admin')
            ->forUser($this->user())
            ->orderByDesc('date')
            ->get();

        $years = $leaves
            ->pluck('date')
            ->map(function ($date) {
                return $date->format('Y');
            })
            ->unique()
            ->toArray();

        return response()->json([
            'types' => mapForSelect(Leave::TYPES),
            'years' => mapForSelect($years, true),
            'leaves' => LeaveResource::collection($leaves),
        ]);
    }
}
