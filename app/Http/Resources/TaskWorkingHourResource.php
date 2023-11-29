<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskWorkingHourResource extends JsonResource
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
            'admin_id' => $this->admin_id,
            'date' => $this->date,
            'time' => $this->time,
            'description' => $this->description,
            'admin' => AdminResource::make($this->whenLoaded('admin')),
            'task' => TaskResource::make($this->whenLoaded('task')),
        ];
    }
}
