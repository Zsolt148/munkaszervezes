<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * App\Models\TaskWorkingHour
 *
 * @property int $id
 * @property int $task_id
 * @property int $admin_id
 * @property string $date
 * @property int $time
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Admin $admin
 * @property-read \App\Models\Task $task
 *
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour query()
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereTaskId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TaskWorkingHour whereUpdatedAt($value)
 * @method static Builder|TaskWorkingHour search(\Illuminate\Http\Request $request)
 *
 * @mixin \Eloquent
 */
class TaskWorkingHour extends Model
{
    use HasFactory;

    protected $table = 'task_working_hours';

    public $fillable = [
        'task_id',
        'admin_id',
        'date',
        'time',
        'description',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function scopeSearch(Builder $query, Request $request): Builder
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $adminId = $request->input('admin_id');

        if ($startDate && $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->whereDate('date', '<=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('date', '>=', $endDate);
        }
        if ($adminId) {
            $query->where('admin_id', $adminId);
        }

        return $query;
    }
}
