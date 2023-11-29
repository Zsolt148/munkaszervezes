<?php

namespace App\Http\Resources;

use App\Models\Activity;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Activity */
class HistoryResource extends JsonResource
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
            'description' => __($this->description),
            'name' => $this->name($this->resource),
            'title' => $this->title($this->resource),
            'causer' => $this->causer,
            'subject' => $this->subject,
            'event' => trans('log.'.$this->event),
            'event_text' => $this->event($this->resource),
            'props' => $this->properties,
            'old_values' => isset($this->properties['old']) ? $this->properties['old'] : '',
            'new_values' => isset($this->properties['attributes']) ? $this->properties['attributes'] : '',
            'properties' => $this->properties,
            'created_at' => $this->created_at,
        ];
    }

    protected function title(Activity $log): string
    {
        $event = $log->event;
        $subject = $log->subject;

        if (! $subject) {
            return '';
        }

        if ($subject instanceof Task) {
            switch ($event) {
                case Activity::CREATED:
                    return 'új feladatot hozott létre';
                case Activity::UPDATED:
                    return 'módosította a feladatot';
                case Activity::DELETED:
                    return 'törölte a feladatot';
            }
        }

        if ($subject instanceof TaskComment) {
            switch ($event) {
                case Activity::CREATED:
                    return 'új kommentet hozott létre';
                case Activity::UPDATED:
                    return 'módosította a kommentet';
                case Activity::DELETED:
                    return 'törölte a kommentet';
            }
        }

        return '';
    }

    protected function event(Activity $log): string
    {
        // App\Models\Settings -> Settings + event
        $name = trans(last(explode('\\', $log->subject_type)));

        return "$name ".trans('log.'.$log->event);
    }

    protected function name(Activity $log): string
    {
        $name = '';

        // subcjet is not deleted
        if ($log->subject && isset($log->subject->name)) {
            $name = $log->subject->name;
        }
        // subject is restored
        elseif (isset($this->properties['attributes'])) {
            $attributes = $this->properties['attributes'];

            if (isset($attributes['name'])) {
                $name .= $attributes['name'];
            }
        }
        // subject is deleted
        elseif (isset($this->properties['old'])) {
            $attributes = $this->properties['old'];

            if (isset($attributes['name'])) {
                $name .= $attributes['name'];
            }
        }

        return $name;
    }
}
