<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Leave
 *
 * @property int $id
 * @property int $admin_id
 * @property string $type
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon|null $accepted_at
 * @property \Illuminate\Support\Carbon|null $declined_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \App\Models\Admin $admin
 * @property-read string $day
 * @property-read string $status
 * @property-read string $status_text
 * @property-read string $year
 *
 * @method static Builder|Leave accepted()
 * @method static Builder|Leave day(?string $day = null)
 * @method static \Database\Factories\LeaveFactory factory($count = null, $state = [])
 * @method static Builder|Leave forUser(?\App\Models\Admin $admin = null)
 * @method static Builder|Leave month(array $month = [])
 * @method static Builder|Leave newModelQuery()
 * @method static Builder|Leave newQuery()
 * @method static Builder|Leave onlyTrashed()
 * @method static Builder|Leave query()
 * @method static Builder|Leave week(array $week = [])
 * @method static Builder|Leave whereAcceptedAt($value)
 * @method static Builder|Leave whereAdminId($value)
 * @method static Builder|Leave whereCreatedAt($value)
 * @method static Builder|Leave whereDate($value)
 * @method static Builder|Leave whereDeclinedAt($value)
 * @method static Builder|Leave whereDeletedAt($value)
 * @method static Builder|Leave whereId($value)
 * @method static Builder|Leave whereType($value)
 * @method static Builder|Leave whereUpdatedAt($value)
 * @method static Builder|Leave withTrashed()
 * @method static Builder|Leave withoutTrashed()
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 *
 * @mixin \Eloquent
 */
class Leave extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    const TYPE_SICK = 'sick';

    const TYPE_LEAVE = 'leave';

    const TYPE_EXTRA_LEAVE = 'extra_leave';

    const TYPES = [
        self::TYPE_SICK => 'Sick',
        self::TYPE_LEAVE => 'Leave',
        self::TYPE_EXTRA_LEAVE => 'Extra leave',
    ];

    protected $fillable = [
        'admin_id',
        'type',
        'date',
        'accepted_at',
        'declined_at',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    protected $appends = [
        'status',
        'day',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function scopeForUser(Builder $query, ?Admin $admin = null): Builder
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        return $query
            ->whereHas('admin', function (Builder $query) use ($admin) {
                return $query->role($admin->allRoles()->pluck('id'));
            });
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
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

    public function getStatusAttribute(): string
    {
        if ($this->accepted_at) {

            $now = now();

            if ($now->lessThan($this->date)) {
                return 'not_started';
            }

            if ($now->isSameDay($this->date)) {
                return 'in_progress';
            }

            return 'done';
        }

        if ($this->declined_at) {
            return 'rejected';
        }

        return 'waiting_for_approve';
    }

    public function getStatusTextAttribute(): string
    {
        if ($this->accepted_at) {

            $now = now();

            if ($now->lessThan($this->date)) {
                return 'Nincs elkezdve';
            }

            if ($now->isSameDay($this->date)) {
                return 'Folyamatban';
            }

            return 'Befejezve';
        }

        if ($this->declined_at) {
            return 'Elutasítva';
        }

        return 'Elfogadásra vár';
    }

    public function getDayAttribute(): string
    {
        return $this->date->dayName;
    }

    public function getYearAttribute(): string
    {
        return $this->date->format('Y');
    }
}
