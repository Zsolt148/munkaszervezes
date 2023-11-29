<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskSearchController extends Controller
{
    public function stories(Request $request)
    {
        $search = $request->get('search');

        if (! $search) {
            return [];
        }

        return TaskResource::collection(
            Task::query()
                ->story()
                ->with('responsible')
                ->search($search)
                ->get()
        );
    }

    public function subtasks(Request $request)
    {
        $search = $request->get('search');

        if (! $search) {
            return [];
        }

        return TaskResource::collection(
            Task::query()
                ->with('responsible')
                ->search($search)
                ->get()
        );
    }
}
