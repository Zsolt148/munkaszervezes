<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MentionEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected Model $user;

    protected Model $model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $user, Model $model)
    {
        $this->user = $user;
        $this->model = $model;
    }

    public function getUser(): Model
    {
        return $this->user;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
