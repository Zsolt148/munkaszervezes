<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\TaskResource;
use App\Models\Admin;
use App\Models\Leave;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskResourcePlanningController extends Controller
{
    const TABS = [
        'group' => 0,
        'worker' => 1,
        'pre' => 2,
    ];

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        return Inertia::render('ResourcePlanning/Index', [
            'views' => mapForSelect(Task::VIEWS),
            'roles' => RoleResource::collection($this->user()->allRoles()),
            'tabs' => mapForSelect(self::TABS),
            'tab' => self::TABS[$request->get('tab')] ?? 0,
        ]);
    }

    public function fetchGroup()
    {
        $this->authorize('viewAny', Task::class);

        $tasks = Task::query()
            ->withoutEagerLoads()
            ->with('role', 'tags', 'responsible', 'park')
            ->where('role_id', null)
            ->withTrashed()
            ->get();

        if ($this->user()->isAdmin()) {
            $roles = Role::query()
                ->with('tasks', 'tasks.tags', 'children', 'users')
                ->where('parent_id', null)
                ->get();
        } else {
            $roles = Role::query()
                ->with('tasks', 'tasks.tags', 'children', 'users')
                ->forUser($this->user(), false)
                ->get();
        }

        return response()->json([
            'roles' => RoleResource::collection($roles),
            'tasks' => TaskResource::collection($tasks),
            'tags' => Tag::getWithType('task_tags'),
        ]);
    }

    public function fetchWorkers(Request $request)
    {
        $tasks = Task::query()
            ->withoutEagerLoads()
            ->with('role', 'tags', 'responsible', 'park')
            ->where('responsible_id', null)
            ->whereIn('role_id', $this->user()->allRoles()->pluck('id'))
            ->withTrashed()
            ->get();

        $rolesQuery = Role::query()
            ->withoutEagerLoads()
            ->forUser($this->user())
            ->with([
                'users',
                'users.tasks.workingHours',
                'users.tasks' => function (HasMany $query) use ($request) {
                    $query
                        ->day($request->get('day'))
                        ->week($request->get('week', []))
                        ->month($request->get('month', []));
                },
                'users.leaves' => function (HasMany $query) use ($request) {
                    return $query
                        ->accepted()
                        ->day($request->get('day'))
                        ->week($request->get('week', []))
                        ->month($request->get('month', []));
                },
            ])
            ->when($request->role, function ($query) use ($request) {
                return $query->where('id', $request->role);
            });

        $roles = $rolesQuery
            ->get()
            ->map(function (Role $role) use ($request) {
                $role->users->transform(function (Admin $admin) use ($request) {

                    // tasks and leaves merge
                    $t = collect($admin->tasks)
                        ->merge($admin->leaves)
                        ->toBase();

                    $admin->current_dates = $this->getDates($request, $t);

                    return $admin;
                });

                return $role;
            });

        return response()->json([
            'roles' => RoleResource::collection($roles),
            'tasks' => TaskResource::collection($tasks),
            'tags' => Tag::getWithType('task_tags'),
            'dates' => $this->getDates($request),
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->fill([
            'role_id' => $request->roleId,
        ]);

        if (isset($request->date) && isset($request->userId)) {
            $task->fill([
                'responsible_id' => $request->userId,
                'date' => $request->date,
            ]);
        }

        $task->save();

        return response()->json([
            'success' => 'Feladat frissítve',
        ]);
    }

    public function remove(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->fill([
            'role_id' => null,
            'responsible_id' => null,
        ]);

        $task->save();

        return response()->json([
            'success' => 'Feladat áthelyezve',
        ]);
    }

    private function getDates(Request $request, $tasks = []): array
    {
        $day = $request->get('day');
        $week = $request->get('week');
        $month = $request->get('month');

        if ($day) {
            return [
                $this->getDate(Carbon::parse($day), $tasks),
            ];
        }

        if ($week || $month) {

            $interval = $week ?? $month;
            sort($interval);

            $period = CarbonPeriod::create($interval[0], $interval[1]);

            $dates = [];
            foreach ($period as $carbon) {
                $dates[] = $this->getDate($carbon, $tasks, $period);
            }

            return $dates;
        }

        return [];
    }

    private function getDate(Carbon $carbon, $tasks = [], $period = null): array
    {
        $datePeriod = null;

        if ($period) {
            $startDate = $period->getStartDate();
            $endDate = $period->getEndDate();
            $datePeriod = $startDate->format('m.d').' - '.$endDate->format('m.d');
        }

        return [
            'datePeriod' => $datePeriod,
            'date' => $carbon->toDateString(),
            'dateDay' => $carbon->day,
            'day' => $carbon->dayName,
            'tasks' => collect($tasks)
                ->filter(function (Task|Leave $task) use ($carbon) {
                    if ($task instanceof Leave) {
                        return $task->date->format('Y-m-d') == $carbon->toDateString();
                    }

                    return $task->date == $carbon->toDateString();
                })
                ->map(function (Task|Leave $task) {
                    if ($task instanceof Leave) {
                        return LeaveResource::make($task);
                    }

                    return TaskResource::make($task);
                })
                ->toArray(),
        ];
    }
}
