<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Services\Tasks\TaskGeneratorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskGeneratorController extends Controller
{
    public function fetchTasks(): JsonResponse
    {
        return response()->json([
            'tasks' => TaskGeneratorService::defaultTasks(),
            'empty_task' => TaskGeneratorService::emptyTask(),
        ]);
    }

    public function createTasks(Request $request): JsonResponse
    {
        $tasks = $request->input('tasks');

        $service = TaskGeneratorService::make($this->user());

        foreach ($tasks as $task) {
            $service
                ->count($task['count'] ?? 0)
                ->properties(TaskGeneratorService::mapProperties($task['properties']))
                ->create();
        }

        return response()->json([
            'success' => trans('Successfully created'),
        ]);
    }
}
