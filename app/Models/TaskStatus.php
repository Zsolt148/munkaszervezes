<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TaskStatus
 *
 * @property int $id
 * @property int $task_id
 * @property string $status
 * @property \Illuminate\Support\Carbon $started_at
 * @property \Illuminate\Support\Carbon|null $ended_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Task $task
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereEndedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskStatus whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class TaskStatus extends Model
{
    protected $fillable = ['status', 'task_id', 'started_at', 'ended_at'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function durationForHumans()
    {
        if ($this->ended_at === null) {
            $duration = $this->started_at->diff(now());
        } else {
            $duration = $this->started_at->diff($this->ended_at);
        }

        $humanDuration = $duration->format('%h hours, %i minutes');
        if ($duration->h === 0) {
            // Remove the hours component if it's zero
            $humanDuration = str_replace('%h hours, ', '', $humanDuration);
        }

        return $humanDuration;
    }
}
