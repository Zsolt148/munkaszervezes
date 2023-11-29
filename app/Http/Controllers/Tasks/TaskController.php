<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    protected TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
        $this->authorizeResource(Task::class);
    }

    public function index(Request $request)
    {
        return Inertia::render('Tasks/Index', array_merge($this->service->props(), [
            'tabs' => mapForSelect(Task::TABS),
            'tab' => Task::TABS[$request->get('tab', 'all')] ?? 0,
        ]));
    }

    public function create()
    {
        return Inertia::render('Tasks/Create');
    }

    public function store(TaskRequest $request)
    {
        $task = new Task();
        $task->createdBy()->associate($this->user());
        $this->service->save($task, $request);

        return redirect()->route('tasks.edit', $task->id)->with('success', __('Successfully created'));
    }

    public function show(Task $task)
    {
        $task->load('role', 'media', 'tags', 'createdBy', 'responsible', 'subtasks', 'workingHours', 'activeWatchers');

        return Inertia::render('Tasks/Show', [
            'task' => TaskResource::make($task),
            'media' => $task->media,
        ]);
    }

    public function edit(Task $task)
    {
        $task->load('media', 'tags', 'createdBy', 'responsible', 'subtasks');

        return Inertia::render('Tasks/Edit', [
            'task' => TaskResource::make($task),
        ]);
    }

    public function update(TaskRequest $request, Task $task)
    {
        $this->service->save($task, $request);

        return redirect()->back()->with('success', trans('Task saved'));
    }

    public function restore(Task $task)
    {
        $task->restore();

        return redirect()->back()->with('success', trans('Task restored'));
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', trans('Task deleted'));
    }

    public function forceDelete(Task $task)
    {
        $task->forceDelete();

        return redirect()->route('tasks.index')->with('success', trans('Successfully force deleted'));
    }
}
