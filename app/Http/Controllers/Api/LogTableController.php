<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class LogTableController extends Controller
{
    public function __invoke(Request $request)
    {
        $perPage = $request->input('per_page');
        $page = $request->input('page');
        $search = $request->input('search');

        $subject_type = $request->input('subject_type');
        $subject_id = $request->input('subject_id');

        $logs = Activity::query()
            ->with('subject', 'causer')
            ->when($subject_type && $subject_id, function (Builder $query) use ($subject_type, $subject_id) {
                return $query
                    ->where('subject_type', $subject_type)
                    ->where('subject_id', $subject_id);
            });

        if ($search) {
            $logs->where(function (Builder $query) use ($search) {
                $query
                    ->where('log_name', 'LIKE', '%'.$search.'%')
                    ->orWhere('event', 'LIKE', '%'.$search.'%')
                    ->orWhere('created_at', 'LIKE', '%'.$search.'%')
                    ->orWhere('description', 'LIKE', '%'.$search.'%')
                    ->orWhereHasMorph('causer', Activity::SEARCHABLE, function (Builder $query) use ($search) {
                        $query->where('name', 'LIKE', '%'.$search.'%');
                    })
                    ->orWhereHasMorph('subject', Activity::SEARCHABLE, function (Builder $query) use ($search) {
                        $query->where('name', 'LIKE', '%'.$search.'%');
                    });
            });
        }

        $logs = $logs
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => LogResource::collection($logs),
            'total' => ceil($logs->total() / $request->input('per_page')),
        ]);
    }
}
