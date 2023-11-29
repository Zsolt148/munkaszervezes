<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\HistoryResource;
use App\Models\Activity;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request, Task $task)
    {
        $perPage = $request->input('per_page') ?? 5;
        $page = $request->input('page') ?? 1;

        $logs = Activity::query()
            ->with('subject', 'causer')
            ->dates($request->get('dates') ?? [])
            ->when(
                $admin = $request->get('admin'),
                fn (Builder $query) => $query->where('causer_id', $admin)
            )
            ->where(fn (Builder $query) => $query
                ->where(fn (Builder $query) => $query
                    ->where('subject_type', Task::class)
                    ->where('subject_id', $task->id)
                )
                ->orWhere(fn (Builder $query) => $query
                    ->where('subject_type', TaskComment::class)
                    ->whereIn('subject_id', $task->comments()->withTrashed()->pluck('id'))
                )
            )
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => HistoryResource::collection($logs),
            'total' => $logs->lastPage(),
        ]);
    }
}
