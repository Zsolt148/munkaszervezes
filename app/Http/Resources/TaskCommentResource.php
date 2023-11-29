<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskCommentResource extends JsonResource
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
            'admin' => [
                'id' => $this->admin->id,
                'name' => $this->admin->name,
                'photo' => $this->admin->profile_photo_url ?? '',
            ],
            'created_at' => $this->created_at->format('Y.m.d H:i'),
            'isEditing' => false,
            'updatedBody' => '',
            'task_id' => $this->task_id,
            'body' => $this->body,
        ];
    }
}
