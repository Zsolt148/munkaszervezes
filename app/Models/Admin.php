<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use App\Traits\HasPermissions;
use App\Traits\HasProfilePhoto;
use App\Traits\HasRoles;
use App\Traits\HasTags;
use App\Traits\TwoFactorAuthenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Scout\Searchable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Models\Admin
 *
 * @property int $id
 * @property int|null $supervisor_id
 * @property string $name
 * @property string $email
 * @property string $locale
 * @property string $occupation_type
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $status
 * @property string $date_time_format
 * @property string|null $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property int $email_notifications
 * @property string|null $remember_token
 * @property int $login_attempts
 * @property string|null $profile_photo_path
 * @property string|null $start_of_employment
 * @property string|null $end_of_employment
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property \Illuminate\Support\Carbon|null $blocked_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Activity> $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TaskComment> $comments
 * @property-read int|null $comments_count
 * @property-read string $profile_photo_url
 * @property-read bool $two_factor_enabled
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Leave> $leaves
 * @property-read int|null $leaves_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PlannedTask> $plannedTasks
 * @property-read int|null $planned_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read Admin|null $supervisor
 * @property-read int|null $tags_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $tasks
 * @property-read int|null $tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $watchingTasks
 * @property-read int|null $watching_tasks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Task> $workingHours
 * @property-read int|null $working_hours_count
 *
 * @method static Builder|Admin active()
 * @method static \Database\Factories\AdminFactory factory($count = null, $state = [])
 * @method static Builder|Admin forUser(?\App\Models\Admin $admin = null)
 * @method static Builder|Admin newModelQuery()
 * @method static Builder|Admin newQuery()
 * @method static Builder|Admin onlyTrashed()
 * @method static Builder|Admin permission($permissions)
 * @method static Builder|Admin query()
 * @method static Builder|Admin role($roles, $guard = null)
 * @method static Builder|Admin whereBlockedAt($value)
 * @method static Builder|Admin whereCreatedAt($value)
 * @method static Builder|Admin whereDateTimeFormat($value)
 * @method static Builder|Admin whereDeletedAt($value)
 * @method static Builder|Admin whereEmail($value)
 * @method static Builder|Admin whereEmailNotifications($value)
 * @method static Builder|Admin whereEmailVerifiedAt($value)
 * @method static Builder|Admin whereEndOfEmployment($value)
 * @method static Builder|Admin whereId($value)
 * @method static Builder|Admin whereLastLoginAt($value)
 * @method static Builder|Admin whereLocale($value)
 * @method static Builder|Admin whereLoginAttempts($value)
 * @method static Builder|Admin whereName($value)
 * @method static Builder|Admin whereOccupationType($value)
 * @method static Builder|Admin wherePassword($value)
 * @method static Builder|Admin whereProfilePhotoPath($value)
 * @method static Builder|Admin whereRememberToken($value)
 * @method static Builder|Admin whereStartOfEmployment($value)
 * @method static Builder|Admin whereStatus($value)
 * @method static Builder|Admin whereSupervisorId($value)
 * @method static Builder|Admin whereTwoFactorRecoveryCodes($value)
 * @method static Builder|Admin whereTwoFactorSecret($value)
 * @method static Builder|Admin whereUpdatedAt($value)
 * @method static Builder|Admin withAllTags(\ArrayAccess|\App\Models\Tag|array|string $tags, ?string $type = null)
 * @method static Builder|Admin withAllTagsOfAnyType($tags)
 * @method static Builder|Admin withAnyTags(\ArrayAccess|\App\Models\Tag|array|string $tags, ?string $type = null)
 * @method static Builder|Admin withAnyTagsOfAnyType($tags)
 * @method static Builder|Admin withTrashed()
 * @method static Builder|Admin withoutTrashed()
 *
 * @mixin \Eloquent
 */
class Admin extends Authenticatable implements MustVerifyEmail
{
    use HasFactory,
        Notifiable,
        HasProfilePhoto,
        HasRoles,
        HasPermissions,
        TwoFactorAuthenticatable,
        LogsActivity,
        SoftDeletes,
        Searchable,
        HasTags;

    protected $table = 'admins';

    const SEARCHABLE_FIELDS = ['id', 'name', 'email'];

    const LOGIN_ATTEMPTS = 5;

    const STATUS_INVITED = 'invited';

    const STATUS_REGISTERED = 'registered';

    const STATUS_BLOCKED = 'blocked';

    const STATUSES = [
        self::STATUS_INVITED => 'Invited',
        self::STATUS_REGISTERED => 'Registered',
        self::STATUS_BLOCKED => 'Blocked',
    ];

    const OCCUPATION_TYPES = [
        'full_time' => 'Full time',
        'part_time' => 'Part time',
        'third_party' => 'Third part',
    ];

    // Moment date formats
    const DATE_TIME_FORMATS = [
        'YYYY.MM.DD LTS',
        'YYYY.MM.DD LT',
        'YYYY.MM.DD',
        'll',
        'LL',
        'LLL',
        'lll',
        'LLLL',
        'llll',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supervisor_id',
        'name',
        'email',
        'status',
        'password',
        'locale',
        'email_notifications',
        'last_login_at',
        'blocked_at',
        'date_time_format',
        'occupation_type',
        'start_of_employment',
        'end_of_employment',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'blocked_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
        'two_factor_enabled',
    ];

    protected $with = [
        'roles',
        'permissions',
    ];

    public function rolesCacheKey(): string
    {
        return 'admin-all-roles-'.$this->id;
    }

    public function toSearchableArray()
    {
        return $this->only(self::SEARCHABLE_FIELDS);
    }

    public function allRoles(): Collection
    {
        return cache()->rememberForever($this->rolesCacheKey(), function () {
            return Role::allRoles($this->roles);
        });
    }

    public function isAdmin(): bool
    {
        return $this->allRoles()->pluck('name')->intersect(Role::admins())->isNotEmpty();
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(self::class, 'supervisor_id')->withTrashed();
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'responsible_id');
    }

    public function plannedTasks(): HasMany
    {
        return $this->hasMany(PlannedTask::class, 'responsible_id');
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class, 'admin_id');
    }

    public function watchingTasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_has_watchers');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(Task::class, 'admin_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->dontLogIfAttributesChangedOnly(['updated_at', 'last_login_at', 'remember_token'])
            ->logOnly($this->fillable)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Beosztott
     */
    public function isSubordinate(?Admin $admin = null): bool
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        return $admin->allRoles()->pluck('id')->contains($this->allRoles()->pluck('id'));
    }

    public function scopeForUser(Builder $query, ?Admin $admin = null): Builder
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        return $query->role($admin->allRoles()->pluck('id'));
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function isInvited(): bool
    {
        return $this->status === self::STATUS_INVITED;
    }

    public function isRegistered(): bool
    {
        return $this->status === self::STATUS_REGISTERED;
    }

    public function isBlocked(): bool
    {
        return $this->status === self::STATUS_BLOCKED;
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', self::STATUS_REGISTERED);
    }

    public function getStatusText()
    {
        switch (true) {
            case $this->isRegistered():
                return __('Registered');
            case $this->isInvited():
                return __('Invited');
            case $this->isBlocked():
                return __('Blocked');
        }
    }

    public function getStatusDate()
    {
        switch (true) {
            case $this->isRegistered():
            case $this->isInvited():
                return $this->created_at;
            case $this->isBlocked():
                return $this->blocked_at;
        }

        return null;
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }
}
