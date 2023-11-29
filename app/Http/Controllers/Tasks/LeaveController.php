<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequest;
use App\Models\Leave;
use App\Models\Task;
use Carbon\CarbonPeriod;

class LeaveController extends Controller
{
    public function store(LeaveRequest $request)
    {
        $this->authorize('create', Leave::class);

        $validated = $request->validated();

        $period = CarbonPeriod::create($validated['dates'][0], $validated['dates'][1]);

        foreach ($period as $carbon) {
            Leave::create([
                'admin_id' => $validated['admin_id'],
                'type' => $validated['type'],
                'date' => $carbon->format('Y-m-d'),
            ]);
        }

        return response()->json([
            'success' => trans('Successfully created'),
        ]);
    }

    public function update(LeaveRequest $request, Leave $leave)
    {
        $this->authorize('update', $leave);

        $validated = $request->validated();

        $leave->update([
            'admin_id' => $validated['admin_id'],
            'type' => $validated['type'],
            'date' => $validated['date'],
        ]);

        return response()->json([
            'success' => trans('Successfully updated'),
        ]);
    }

    public function accept(Leave $leave)
    {
        $this->authorize('update', $leave);

        $tasks = Task::query()
            ->where('responsible_id', $leave->admin_id)
            ->where('date', $leave->date)
            ->get();

        if ($tasks->isNotEmpty()) {
            return response()->json([
                'error' => 'Nem fogadható el mert erre a napra már van feladata',
            ]);
        }

        $leave->update([
            'accepted_at' => now(),
            'declined_at' => null,
        ]);

        return response()->json([
            'success' => trans('Successfully updated'),
        ]);
    }

    public function decline(Leave $leave)
    {
        $this->authorize('update', $leave);

        $leave->update([
            'accepted_at' => null,
            'declined_at' => now(),
        ]);

        return response()->json([
            'success' => trans('Successfully updated'),
        ]);
    }

    public function destroy(Leave $leave)
    {
        $this->authorize('delete', $leave);

        $leave->delete();

        return response()->json([
            'success' => trans('Successfully deleted'),
        ]);
    }
}
