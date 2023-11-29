<?php

namespace App\Events;

use App\Models\Admin;
use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $task;

    public $admin;

    public function __construct(Task $task, Admin $admin)
    {
        $this->task = $task;
        $this->admin = $admin;
    }
}
