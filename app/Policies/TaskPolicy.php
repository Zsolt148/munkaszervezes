<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Role;
use App\Models\Task;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * @param  \App\Models\Admin  $user
     * @param  string  $ability
     * @return void|bool
     */
    public function before(Admin $admin, $ability)
    {
        if ($admin->hasRole(Role::admins())) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(Admin $admin)
    {
        return $admin->hasPermissionTo('tasks');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(Admin $admin, Task $task)
    {
        return $admin->hasPermissionTo('tasks') || $admin->hasPermissionTo('resource_planning');
    }

    /**
     * Determine whether the user can create models.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(Admin $admin)
    {
        return $admin->hasPermissionTo('tasks');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(Admin $admin, Task $task)
    {
        return $admin->hasPermissionTo('tasks') || $admin->hasPermissionTo('resource_planning');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(Admin $admin, Task $task)
    {
        return $admin->hasPermissionTo('tasks') || $admin->hasPermissionTo('resource_planning');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(Admin $admin, Task $task)
    {
        return $admin->hasPermissionTo('tasks') || $admin->hasPermissionTo('resource_planning');
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(Admin $admin, Task $task)
    {
        return $admin->hasPermissionTo('tasks') || $admin->hasPermissionTo('resource_planning');
    }
}
