<?php

namespace App\Models;

use App\Helpers\Interfaces\MentionInterface;
use App\Traits\HasMentions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\TaskComment
 *
 * @property int $id
 * @property int $task_id
 * @property int $admin_id
 * @property string $body
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin $admin
 * @property-read \App\Models\Task $task
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereUpdatedAt($value)
 *
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskComment withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TaskComment extends Model implements MentionInterface
{
    use HasFactory, HasMentions, LogsActivity, SoftDeletes;

    protected $fillable = [
        'task_id',
        'admin_id',
        'body',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public static function mentionField(): string
    {
        return 'body';
    }

    public function mentionTitle(): string
    {
        return 'Megjelöltek egy feladat kommentben';
    }

    public function mentionBody(): string
    {
        return "Megjelöltek téged a(z) {$this->task->name} feladatban.";
    }

    public function mentionRoute(): string
    {
        return route('tasks.show', $this->task->id);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class)->withTrashed();
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class)->withTrashed();
    }
}
