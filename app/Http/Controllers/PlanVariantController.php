<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanVariantRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\PlannedTaskResource;
use App\Http\Resources\PlanVariantResource;
use App\Http\Resources\RoleResource;
use App\Models\Admin;
use App\Models\Leave;
use App\Models\PlannedTask;
use App\Models\PlanVariant;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Task;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanVariantController extends Controller
{
    public function store(PlanVariantRequest $request)
    {
        $variant = new PlanVariant();
        $variant->name = $request->name;
        $variant->save();

        return redirect()->back()->with('success', trans('Successfully updated'));
    }

    public function update(PlanVariantRequest $request, PlanVariant $variant)
    {
        $variant->fill([
            'name' => $request['name'],
        ]);

        $variant->save();
    }

    public function destroy(PlanVariantRequest $request)
    {
        $variant = PlanVariant::findOrFail($request->id);

        $variant->delete();

        return redirect()->back()->with('success', trans('Plan deleted'));
    }

    public function importCurrentPlan(Request $request): JsonResponse
    {
        PlannedTask::where('variant_id', $request->variantId)->delete();

        $tasks = Task::whereNotNull('responsible_id')->whereNotNull('role_id')->get();

        foreach ($tasks as $task) {
            PlannedTask::updateOrCreate([
                'task_id' => $task->id,
                'variant_id' => $request->variantId,
            ],
                $task->only(['responsible_id', 'role_id', 'date'])
            );
        }

        return response()->json(['success' => 'Jelenlegi terv importálása megtörtént']);
    }

    public function override(PlanVariant $variant): JsonResponse
    {
        $plannedTasks = PlannedTask::query()->where('variant_id', $variant->id)->get();

        //        Task::query()
        //			->whereNotIn('id', $plannedTasks->pluck('id'))
        //			->update([
        //				'role_id' => null,
        //				'responsible_id' => null,
        //				'date' => null
        //			]);

        foreach ($plannedTasks as $plannedTask) {
            Task::query()
                ->firstWhere('id', $plannedTask->task_id)
                ->updateQuietly([
                    'responsible_id' => $plannedTask->responsible_id,
                    'role_id' => $plannedTask->role_id,
                    'date' => $plannedTask->date,
                ]);
        }

        return response()->json(['success' => 'Jelenlegi beosztás felülírva']);
    }

    public function removePlanTask($task, $variant): JsonResponse
    {
        $pt = PlannedTask::query()->where('variant_id', $variant)->where('task_id', $task)->firstOrFail();

        $pt->delete();

        return response()->json([
            'success' => 'Feladat áthelyezve',
        ]);
    }

    public function storePlanTask(Request $request): JsonResponse
    {
        PlannedTask::updateOrCreate([
            'task_id' => $request->taskId,
            'variant_id' => $request->variantId,
        ], [
            'responsible_id' => $request->userId,
            'role_id' => $request->roleId,
            'date' => $request->date,
        ]);

        return response()->json(['success' => 'Plan feladat státusz frissítve']);
    }

    public function fetchVariants(Request $request): JsonResponse
    {
        $variants = PlanVariant::query()->get();

        return response()->json([
            'variants' => PlanVariantResource::collection($variants),
        ]);
    }

    public function fetchTasks(Request $request): JsonResponse
    {
        $plannedTasks = PlannedTask::query()
            ->where('variant_id', $request->get('variantId'))
            ->get();

        $plans = Task::query()
            ->withoutEagerLoads()
            ->with('role', 'tags', 'responsible')
            ->withTrashed()
            ->get()
            ->reject(function (Task $task) use ($plannedTasks) {
                return in_array($task->id, $plannedTasks->pluck('task_id')->toArray());
            })
            ->map(function (Task $task) {

                // Fake plan task cards for the left side
                $plan = new PlannedTask();
                $plan->variant_id = null;
                $plan->date = null;
                $plan->responsible_id = null;
                $plan->task_id = $task->id;

                return $plan;
            });

        return response()->json([
            'tasks' => PlannedTaskResource::collection($plans),
            'tags' => Tag::getWithType('task_tags'),
        ]);
    }

    public function fetchData(Request $request): JsonResponse
    {
        $roles = Role::query()
            ->withoutEagerLoads()
            ->forUser($this->user())
            ->with([
                'users',
                'users.plannedTasks.task',
                'users.plannedTasks' => function (HasMany $query) use ($request) {
                    return $query
                        ->where('variant_id', $request->get('variantId'))
                        ->day($request->get('day'))
                        ->week($request->get('week', []))
                        ->month($request->get('month', []));
                },
            ])
            ->when($request->role, function ($query) use ($request) {
                return $query->where('id', $request->role);
            })
            ->get()
            ->map(function (Role $role) use ($request) {
                $role->users->transform(function (Admin $admin) use ($request) {

                    $tasks = collect($admin->plannedTasks)
//                        ->merge($admin->leaves)
                        ->toBase();

                    $admin->current_dates = $this->getDates($request, $tasks);

                    return $admin;
                });

                return $role;
            });

        return response()->json([
            'roles' => RoleResource::collection($roles),
            'dates' => $this->getDates($request),
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
            'planned_tasks' => collect($tasks)
                ->filter(function (PlannedTask|Leave $task) use ($carbon) {
                    //                    if ($task instanceof Leave) {
                    //                        return $task->date->format('Y-m-d') == $carbon->toDateString();
                    //                    }
                    return $task->date == $carbon->toDateString();
                })
                ->map(function (PlannedTask|Leave $task) {
                    //                    if ($task instanceof Leave) {
                    //                        return LeaveResource::make($task);
                    //                    }
                    return PlannedTaskResource::make($task);
                })
                ->toArray(),
        ];
    }
}
