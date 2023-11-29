<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'status' => $this->status,
            'statusText' => trans('tasks.'.$this->status),
            'duration' => $this->durationForHumans(),
            'started_at' => $this->started_at,
            'ended_at' => $this->ended_at,
        ];
    }
}
