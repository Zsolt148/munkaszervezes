<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\Request;

class TaskPropsController extends Controller
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        return response()->json($this->service->props());
    }
}
