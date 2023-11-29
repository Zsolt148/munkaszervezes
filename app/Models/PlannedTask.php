<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PlannedTask
 *
 * @property int $id
 * @property int $variant_id
 * @property int $task_id
 * @property int $role_id
 * @property int $responsible_id
 * @property string $date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin|null $responsible
 * @property-read \App\Models\Task $task
 * @property-read \App\Models\PlanVariant $variant
 *
 * @method static Builder|PlannedTask day(?string $day = null)
 * @method static Builder|PlannedTask month(array $month = [])
 * @method static Builder|PlannedTask newModelQuery()
 * @method static Builder|PlannedTask newQuery()
 * @method static Builder|PlannedTask query()
 * @method static Builder|PlannedTask week(array $week = [])
 * @method static Builder|PlannedTask whereCreatedAt($value)
 * @method static Builder|PlannedTask whereDate($value)
 * @method static Builder|PlannedTask whereId($value)
 * @method static Builder|PlannedTask whereResponsibleId($value)
 * @method static Builder|PlannedTask whereRoleId($value)
 * @method static Builder|PlannedTask whereTaskId($value)
 * @method static Builder|PlannedTask whereUpdatedAt($value)
 * @method static Builder|PlannedTask whereVariantId($value)
 *
 * @mixin \Eloquent
 */
class PlannedTask extends Model
{
    use HasFactory;

    protected $table = 'planned_tasks';

    protected $fillable = [
        'variant_id',
        'task_id',
        'responsible_id',
        'role_id',
        'date',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'responsible_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(PlanVariant::class, 'variant_id');
    }

    public function scopeDay(Builder $query, string $day = null): Builder
    {
        if (! $day) {
            return $query;
        }

        $day = Carbon::parse($day)->format('Y-m-d');

        return $query
            ->whereDate('date', $day);
    }

    public function scopeWeek(Builder $query, array $week = []): Builder
    {
        if (empty($week)) {
            return $query;
        }

        sort($week);

        return $query
            ->whereBetween('date', [$week[0], $week[1]]);
    }

    public function scopeMonth(Builder $query, array $month = []): Builder
    {
        if (empty($month)) {
            return $query;
        }

        sort($month);

        return $query
            ->whereBetween('date', [$month[0], $month[1]]);
    }
}
