<?php

namespace App\Helpers\Interfaces;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface RoleInterface
{
    /**
     * A role may be given various permissions.
     */
    public function permissions(): BelongsToMany;

    /**
     * Find a role by its name and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findByName(string $name, $guardName): self;

    /**
     * Find a role by its id and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     *
     * @throws \Spatie\Permission\Exceptions\RoleDoesNotExist
     */
    public static function findById(int $id, $guardName): self;

    /**
     * Find or create a role by its name and guard name.
     *
     * @param  string|null  $guardName
     * @return \Spatie\Permission\Contracts\Role
     */
    public static function findOrCreate(string $name, $parent_id, $guardName): self;

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  string|PermissionInterface  $permission
     */
    public function hasPermissionTo($permission): bool;
}
