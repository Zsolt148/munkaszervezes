<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Notifications\Task\TaskCreatedNotification;

class SendNewTaskNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(TaskCreated $event)
    {
        $event->admin->notify(new TaskCreatedNotification($event->task));
    }
}
