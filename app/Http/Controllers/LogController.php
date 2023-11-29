<?php

namespace App\Http\Controllers;

use App\Http\Resources\LogResource;
use App\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LogController extends Controller
{
    /**
     * @return \Inertia\Response
     */
    public function index()
    {
        Gate::authorize('viewAny', Activity::class);

        $logs = Activity::query()
            ->with('subject', 'causer')
            ->latest('id')
            ->get();

        return Inertia::render('Logs/Index', [
            'logs' => LogResource::collection($logs),
        ]);
    }
}
