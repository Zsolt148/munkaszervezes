<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'park' => $this->whenLoaded('park'),

            'description' => $this->description ?: '',
            'created_by' => $this->whenLoaded('createdBy'),
            'responsible' => $this->whenLoaded('responsible'),
            'responsible_id' => $this->responsible_id,
            'role' => $this->whenLoaded('role'),
            'role_id' => $this->role_id,
            'media' => $this->whenLoaded('media', fn () => $this->media->toArray()),
            'watchers' => $this->whenLoaded('activeWatchers'),
            'watchersText' => $this->whenLoaded('activeWatchers', fn () => $this->activeWatchers->pluck('name')->join(', ')),
            'is_watching' => $this->whenLoaded('activeWatchers', fn () => $this->isBeingWatchedByCurrentUser()),
            'working_hours' => $this->whenLoaded('workingHours'),
            'working_hours_sum' => $this->whenLoaded('workingHours', fn () => $this->workingHours->sum('time')),

            'subtasks' => TaskResource::collection($this->whenLoaded('subtasks')),
            'parent' => TaskResource::make($this->whenLoaded('parent')),
            'tags' => $this->tagsForTypeSelect('task_tags'),

            'roleText' => $this->whenLoaded('role', fn () => trans('role.'.$this->role->name)),
            'taskTypeText' => trans('tasks.'.$this->task_type),
            'statusText' => trans('tasks.'.$this->status),
            'priorityText' => trans('tasks.'.$this->priority),

            'updated_at' => $this->updated_at->format('Y-m-d H:i'),
            'updated_at_diff' => $this->updated_at->diffForHumans(),
            'created_at' => $this->created_at->format('Y-m-d'),
            'timeInCurrentStatus' => $this->status_changed_at->diffForHumans(),
            'done_at' => $this->done_at?->format('Y-m-d'),

            'taskStatuses' => $this->whenLoaded('taskStatuses', function () {
                $this->taskStatuses->map(function ($taskStatus) {
                    return ['status' => trans('tasks.'.$taskStatus->status), 'duration' => $taskStatus->durationForHumans()];
                });
            }),
            'trashed' => $this->trashed(),
            'class' => Task::class,
        ]);
    }
}
