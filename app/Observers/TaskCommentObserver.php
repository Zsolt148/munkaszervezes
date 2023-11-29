<?php

namespace App\Observers;

use App\Models\TaskComment;
use App\Notifications\Task\TaskCommentCreated;

class TaskCommentObserver
{
    /**
     * Handle the TaskComment "created" event.
     *
     * @return void
     */
    public function created(TaskComment $taskComment)
    {
        $taskComment->load('task', 'task.responsible', 'task.activeWatchers');
        $task = $taskComment->task;

        $task->notify(new TaskCommentCreated($task, $taskComment));
    }

    /**
     * Handle the TaskComment "updated" event.
     *
     * @return void
     */
    public function updated(TaskComment $taskComment)
    {
        //$taskComment->load('task', 'task.responsible', 'task.activeWatchers');
    }

    /**
     * Handle the TaskComment "deleted" event.
     *
     * @return void
     */
    public function deleted(TaskComment $taskComment)
    {
        //
    }

    /**
     * Handle the TaskComment "restored" event.
     *
     * @return void
     */
    public function restored(TaskComment $taskComment)
    {
        //
    }

    /**
     * Handle the TaskComment "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(TaskComment $taskComment)
    {
        //
    }
}
