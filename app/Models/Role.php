<?php

namespace App\Models;

use App\Helpers\Interfaces\RoleInterface;
use App\Helpers\PermissionRegistrar;
use App\Traits\HasPermissions;
use App\Traits\RefreshesPermissionCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\Permission\Exceptions\GuardDoesNotMatch;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;

/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int|null $parent_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Role[] $children
 * @property-read int|null $children_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\File[] $files
 * @property-read int|null $files_count
 * @property-read Role|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Admin[] $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role permission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProcessDescription[] $processDescriptions
 * @property-read int|null $process_descriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Task[] $tasks
 * @property-read int|null $tasks_count
 *
 * @method static Builder|Role forUser(?\App\Models\Admin $admin = null, bool $allRoles = true)
 *
 * @mixin \Eloquent
 */
class Role extends Model implements RoleInterface
{
    use HasPermissions;
    use RefreshesPermissionCache;

    protected $fillable = [
        'name',
        'guard_name',
        'parent_id',
    ];

    public static function admins(): Collection
    {
        return collect(config('permission.admin_roles', ['superadmin']));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'role_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->with('children', 'tasks', 'users');
    }

    public static function allRoles(Collection $roles): Collection
    {
        if ($roles->pluck('name')->intersect(static::admins())->isNotEmpty()) {
            return cache()->rememberForever('all-roles', function () {
                return Role::all();
            });
        }

        // cache role children
        return $roles->map(function (Role $role) {
            return cache()->rememberForever('role-children-'.$role->id, function () use ($role) {
                if (! $role->relationLoaded('children')) {
                    $role->load('children');
                }

                return [
                    'role' => $role,
                    'children' => static::allRoles($role->children), // rekurzió
                ];
            });
        })->flatten()->values();
    }

    public function scopeForUser(Builder $query, ?Admin $admin = null, bool $allRoles = true): Builder
    {
        if (! $admin) {
            $admin = auth('admin')->user();
        }

        $roles = $allRoles
            ? $admin->allRoles()
            : $admin->roles;

        return $query
            ->whereIn('id', $roles->pluck('id'));
    }

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? config('auth.defaults.guard');

        parent::__construct($attributes);

        $this->guarded[] = $this->primaryKey;
    }

    /**
     * @param  int|null  $parent_id
     * @param  string|null  $guardName
     * @return RoleContract
     */
    public static function findOrCreate(string $name, $parent_id = null, $guardName = null): RoleInterface
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            return static::query()->create(
                ['name' => $name, 'parent_id' => $parent_id, 'guard_name' => $guardName] + (PermissionRegistrar::$teams ? [PermissionRegistrar::$teamsKey => getPermissionsTeamId()] : [])
            );
        }

        return $role;
    }

    public function getTable()
    {
        return config('permission.table_names.roles', parent::getTable());
    }

    public static function create(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? Guard::getDefaultName(static::class);

        $params = ['name' => $attributes['name'], 'guard_name' => $attributes['guard_name']];
        if (PermissionRegistrar::$teams) {
            if (array_key_exists(PermissionRegistrar::$teamsKey, $attributes)) {
                $params[PermissionRegistrar::$teamsKey] = $attributes[PermissionRegistrar::$teamsKey];
            } else {
                $attributes[PermissionRegistrar::$teamsKey] = getPermissionsTeamId();
            }
        }
        if (static::findByParam($params)) {
            throw RoleAlreadyExists::create($attributes['name'], $attributes['guard_name']);
        }

        return static::query()->create($attributes);
    }

    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.models.permission'),
            config('permission.table_names.role_has_permissions'),
            PermissionRegistrar::$pivotRole,
            PermissionRegistrar::$pivotPermission
        );
    }

    /**
     * A role belongs to some users of the model associated with its guard.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_roles'),
            PermissionRegistrar::$pivotRole,
            config('permission.column_names.model_morph_key')
        );
    }

    /**
     * Find a role by its name and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role|\Spatie\Permission\Models\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName = null): RoleInterface
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam(['name' => $name, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }

    /**
     * Find a role by its id (and optionally guardName).
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role|\Spatie\Permission\Models\Role
     */
    public static function findById(int $id, $guardName = null): RoleInterface
    {
        $guardName = $guardName ?? Guard::getDefaultName(static::class);

        $role = static::findByParam([(new static())->getKeyName() => $id, 'guard_name' => $guardName]);

        if (! $role) {
            throw RoleDoesNotExist::withId($id);
        }

        return $role;
    }

    protected static function findByParam(array $params = [])
    {
        $query = static::query();

        if (PermissionRegistrar::$teams) {
            $query->where(function ($q) use ($params) {
                $q->whereNull(PermissionRegistrar::$teamsKey)
                    ->orWhere(PermissionRegistrar::$teamsKey, $params[PermissionRegistrar::$teamsKey] ?? getPermissionsTeamId());
            });
            unset($params[PermissionRegistrar::$teamsKey]);
        }

        foreach ($params as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  string|Permission  $permission
     *
     * @throws \Spatie\Permission\Exceptions\GuardDoesNotMatch
     */
    public function hasPermissionTo($permission): bool
    {
        if (config('permission.enable_wildcard_permission', false)) {
            return $this->hasWildcardPermission($permission, $this->getDefaultGuardName());
        }

        $permissionClass = $this->getPermissionClass();

        if (is_string($permission)) {
            $permission = $permissionClass->findByName($permission, $this->getDefaultGuardName());
        }

        if (is_int($permission)) {
            $permission = $permissionClass->findById($permission, $this->getDefaultGuardName());
        }

        if (! $this->getGuardNames()->contains($permission->guard_name)) {
            throw GuardDoesNotMatch::create($permission->guard_name, $this->getGuardNames());
        }

        return $this->permissions->contains($permission->getKeyName(), $permission->getKey());
    }
}
