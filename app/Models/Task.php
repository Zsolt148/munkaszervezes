<?php

namespace App\Models;

use App\Helpers\Interfaces\MentionInterface;
use App\Traits\HasMentions;
use App\Traits\HasRoles;
use App\Traits\HasTags;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * App\Models\Task
 *
 * @property int $id
 * @property int $created_by
 * @property int $park_id
 * @property int|null $role_id
 * @property int|null $responsible_id
 * @property int|null $parent_id
 * @property string $task_type
 * @property string $status
 * @property string|null $priority
 * @property string $name
 * @property string|null $description
 * @property string $deadline
 * @property string|null $date
 * @property int $estimated_hour
 * @property float|null $travel_time
 * @property \Illuminate\Support\Carbon|null $done_at
 * @property \Illuminate\Support\Carbon $status_changed_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Admin> $activeWatchers
 * @property-read int|null $active_watchers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskComment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Admin $createdBy
 * @property-read \App\Models\TaskStatus|null $latestStatus
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read Task|null $parent
 * @property-read \App\Models\Park $park
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Admin|null $responsible
 * @property-read \App\Models\Role|null $role
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Task> $subtasks
 * @property-read int|null $subtasks_count
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskStatus> $taskStatuses
 * @property-read int|null $task_statuses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Admin> $watchers
 * @property-read int|null $watchers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskWorkingHour> $workingHours
 * @property-read int|null $working_hours_count
 *
 * @method static Builder|Task day(?string $day = null)
 * @method static Builder|Task done()
 * @method static \Database\Factories\TaskFactory factory($count = null, $state = [])
 * @method static Builder|Task forUser(?\App\Models\Admin $admin = null)
 * @method static Builder|Task month(array $month = [])
 * @method static Builder|Task newModelQuery()
 * @method static Builder|Task newQuery()
 * @method static Builder|Task notDone()
 * @method static Builder|Task onlyTrashed()
 * @method static Builder|Task permission($permissions)
 * @method static Builder|Task query()
 * @method static Builder|Task responsibleId(?\App\Models\Admin $admin = null)
 * @method static Builder|Task role($roles, $guard = null)
 * @method static Builder|Task roleScope($roles, $guard = null)
 * @method static Builder|Task search(?string $search = null)
 * @method static Builder|Task searchByUser(\Illuminate\Http\Request $request)
 * @method static Builder|Task story($story = true)
 * @method static Builder|Task todo()
 * @method static Builder|Task week(array $week = [])
 * @method static Builder|Task whereCreatedAt($value)
 * @method static Builder|Task whereCreatedBy($value)
 * @method static Builder|Task whereDate($value)
 * @method static Builder|Task whereDeadline($value)
 * @method static Builder|Task whereDeletedAt($value)
 * @method static Builder|Task whereDescription($value)
 * @method static Builder|Task whereDoneAt($value)
 * @method static Builder|Task whereEstimatedHour($value)
 * @method static Builder|Task whereId($value)
 * @method static Builder|Task whereName($value)
 * @method static Builder|Task whereParentId($value)
 * @method static Builder|Task whereParkId($value)
 * @method static Builder|Task wherePriority($value)
 * @method static Builder|Task whereResponsibleId($value)
 * @method static Builder|Task whereRoleId($value)
 * @method static Builder|Task whereStatus($value)
 * @method static Builder|Task whereStatusChangedAt($value)
 * @method static Builder|Task whereTaskType($value)
 * @method static Builder|Task whereTravelTime($value)
 * @method static Builder|Task whereUpdatedAt($value)
 * @method static Builder|Task withAllTags(\ArrayAccess|\App\Models\Tag|array|string $tags, ?string $type = null)
 * @method static Builder|Task withAllTagsOfAnyType($tags)
 * @method static Builder|Task withAnyTags(\ArrayAccess|\App\Models\Tag|array|string $tags, ?string $type = null)
 * @method static Builder|Task withAnyTagsOfAnyType($tags)
 * @method static Builder|Task withTrashed()
 * @method static Builder|Task withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Task extends Model implements MentionInterface, HasMedia
{
    use HasFactory, SoftDeletes, HasRoles, HasMentions, InteractsWithMedia, HasTags, Searchable, LogsActivity {
        HasRoles::scopeRole as scopeRoleScope;
    }

    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'outstanding' => 'Outstanding',
    ];

    // Ticket tipus
    const TASK_TYPES = [
        'task' => 'Task',
        'story' => 'Story',
        'error' => 'Error',
    ];

    const VIEWS = [
        'daily' => 'Napi nézet',
        'weekly' => 'Heti nézet',
        'monthly' => 'Havi nézet',
    ];

    const STATUS_TODO = 'todo';

    const STATUS_IN_PROGRESS = 'in_progress';

    const STATUS_WAITING = 'waiting';

    const STATUS_WAITING_FOR_REPAIR = 'waiting_for_repair';

    const STATUS_DONE = 'done';

    const STATUSES = [
        self::STATUS_TODO => 'Todo',
        self::STATUS_IN_PROGRESS => 'In progress',
        self::STATUS_WAITING => 'Waiting',
        self::STATUS_WAITING_FOR_REPAIR => 'Waiting for repair',
        self::STATUS_DONE => 'Done',
    ];

    // tabs for v-tab
    const TABS = [
        'all' => 0,
        'done' => 1,
        'leaves' => 2,
    ];

    const SEARCHABLE_FIELDS = ['name', 'description'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'created_by',
        'role_id',
        'park_id',
        'responsible_id',
        'parent_id',
        'task_type',
        'status',
        'priority',
        'name',
        'description',
        'deadline',
        'date',
        'travel_time',
        'estimated_hour',
        'done_at',
        'status_changed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'created_by' => 'integer',
        'role_id' => 'integer',
        'responsible_id' => 'integer',
        'done_at' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    protected $with = [
        'park',
    ];

    public static function mentionField(): string
    {
        return 'description';
    }

    public function mentionTitle(): string
    {
        return "Megjelöltek a(z) $this->name feladatban";
    }

    public function mentionBody(): string
    {
        return "Megjelöltek a(z) $this->name feladatban";
    }

    public function mentionRoute(): string
    {
        return route('tasks.show', $this->id);
    }

    public function toSearchableArray()
    {
        return $this->only(self::SEARCHABLE_FIELDS);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by')->withTrashed();
    }

    public function park(): BelongsTo
    {
        return $this->belongsTo(Park::class, 'park_id');
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(TaskWorkingHour::class, 'task_id');
    }

    public function latestStatus(): HasOne
    {
        return $this->hasOne(TaskStatus::class)->latestOfMany();
    }

    public function taskStatuses(): HasMany
    {
        return $this->hasMany(TaskStatus::class)->latest();
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function watchers(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'task_has_watchers')->withTimestamps();
    }

    public function activeWatchers(): BelongsToMany
    {
        return $this->watchers()->active();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->with('admin');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'responsible_id')->withTrashed();
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('role', 'responsible');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function isBeingWatchedByCurrentUser(): bool
    {
        return $this->watchers->contains('id', auth('admin')->id());
    }

    public function scopeForUser(Builder $query, ?Admin $admin = null): Builder
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        return $query->where(fn (Builder $query) => $query
            ->whereIn('role_id', $admin->allRoles()->pluck('id'))
            ->orWhere('responsible_id', $admin->id)
        );
    }

    public function scopeResponsibleId(Builder $query, ?Admin $admin = null): Builder
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        return $query->where('responsible_id', $admin->id);
    }

    public function scopeStory(Builder $query, $story = true): Builder
    {
        return $query->where('task_type', 'story');
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

    public function scopeSearch(Builder $query, string $search = null): Builder
    {
        return $query->where('name', 'like', "%$search%");
    }

    public function scopeSearchByUser(Builder $query, Request $request): Builder
    {
        $adminId = $request->input('admin_id');

        if ($adminId) {
            $query->where('responsible_id', $adminId);
        }

        return $query;
    }

    public function scopeTodo(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_TODO);
    }

    public function scopeNotDone(Builder $query): Builder
    {
        return $query->where('status', '<>', self::STATUS_DONE);
    }

    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_DONE);
    }

    /**
     * Notifies active watchers and responsible about task changes
     * Expect the current user
     */
    public function notify(Notification $notification): void
    {
        $this->loadMissing('activeWatchers', 'responsible');

        $admins = $this->activeWatchers
            ->push($this->responsible)
            ->unique('id')
            ->reject(function ($admin) {
                return $admin === null || $admin->id === auth('admin')->id();
            })
            ->values()
            ->toBase();

        if ($admins->isEmpty()) {
            return;
        }

        NotificationFacade::send($admins, $notification);
    }
}
