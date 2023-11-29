<?php

namespace App\Http\Resources;

use App\Models\Leave;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Leave */
class LeaveResource extends JsonResource
{
    public function toArray($request)
    {
        return array_merge(parent::toArray($request), [
            'admin' => AdminResource::make($this->whenLoaded('admin')),
            'type_text' => trans(Leave::TYPES[$this->type]),
            'date_text' => $this->date->translatedFormat('Y. M d.'),
            'year' => $this->year,
            'day' => $this->day,
            'status' => $this->status,
            'status_text' => $this->status_text,
        ]);
    }
}
