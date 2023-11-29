<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Leave;
use App\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeavePolicy
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

    public function viewAny(Admin $user): bool
    {
        return true;
    }

    public function view(Admin $user, Leave $leave): bool
    {
        return true;
    }

    public function create(Admin $user): bool
    {
        return true;
    }

    public function update(Admin $user, Leave $leave): bool
    {
        return true;
    }

    public function delete(Admin $user, Leave $leave): bool
    {
        return true;
    }
}
