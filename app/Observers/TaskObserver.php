<?php

namespace App\Observers;

use App\Models\Task;
use App\Notifications\Task\TaskCreatedNotification;
use App\Notifications\Task\TaskDeletedNotification;
use App\Notifications\Task\TaskUpdatedNotification;
use App\Services\Tasks\TaskService;

class TaskObserver
{
    public TaskService $service;

    public function __construct(TaskService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the Task "created" event.
     *
     * @return void
     */
    public function created(Task $task)
    {
        $this->service->createTaskStatus($task);

        $task->notify(new TaskCreatedNotification($task));
    }

    /**
     * Handle the Task "updated" event.
     *
     * @return void
     */
    public function updated(Task $task)
    {
        if ($task->wasChanged('status')) {
            $this->service->createTaskStatus($task);
        }

        $task->notify(new TaskUpdatedNotification($task));
    }

    /**
     * Handle the Task "deleted" event.
     *
     * @return void
     */
    public function deleted(Task $task)
    {
        $task->loadMissing('activeWatchers', 'responsible');

        $task->notify(new TaskDeletedNotification($task));
    }

    /**
     * Handle the Task "restored" event.
     *
     * @return void
     */
    public function restored(Task $task)
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Task $task)
    {
        //
    }
}
