<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
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
            'value' => $this->id,
            'name' => $this->name,
            'text' => __('role.'.$this->name),
            'children' => RoleResource::collection($this->whenLoaded('children', function () {
                return $this->children->map(function ($child) {
                    return new RoleResource($child);
                });
            })),
            'parent_id' => $this->parent_id,
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
            'users' => AdminResource::collection($this->whenLoaded('users')),
            'task_count' => $this->whenLoaded('tasks', fn () => $this->tasks->count()),
        ];
    }
}
