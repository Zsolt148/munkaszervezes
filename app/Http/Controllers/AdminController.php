<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Http\Resources\RoleResource;
use App\Models\Admin;
use App\Models\Role;
use App\Notifications\AdminInvitedNotification;
use App\Traits\AdminForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class AdminController extends Controller
{
    use AdminForm;

    public function index()
    {
        Gate::authorize('viewAny', Admin::class);

        $admins = Admin::query()
            ->with('roles', 'tags')
            ->latest()
            ->get();

        $deletedAdmins = Admin::query()
            ->with('roles', 'tags')
            ->onlyTrashed()
            ->latest()
            ->get();

        return Inertia::render('Admins/Index', [
            'admins' => AdminResource::collection($admins),
            'deletedAdmins' => AdminResource::collection($deletedAdmins),
            'statuses' => mapForSelect(Admin::STATUSES),
            'roles' => RoleResource::collection(Role::all()),
        ]);
    }

    public function create()
    {
        Gate::authorize('create', Admin::class);

        $supervisors = Admin::query()
            ->withoutEagerLoads() // no need for roles and permissions
            ->get();

        return Inertia::render('Admins/Create', [
            'admins' => AdminResource::collection($supervisors),
            'occupation_types' => mapForSelect(Admin::OCCUPATION_TYPES),
            'roles' => RoleResource::collection(Role::all()),
        ]);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Admin::class);

        $validated = $request->validate($this->adminFormRules(null, true));

        /** @var Admin $admin */
        $admin = Admin::create([
            'supervisor_id' => $validated['supervisor_id'] ?? null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => Admin::STATUS_INVITED,
            'occupation_type' => $validated['occupation_type'],
            'start_of_employment' => $validated['start_of_employment'],
        ]);

        $admin->syncRoles($validated['roles'] ?? []);

        $admin->notify(new AdminInvitedNotification(Password::broker('admins')->createToken($admin)));

        return redirect()->route('admins.index')->with('success', __('Successfully created'));

    }

    public function show(Admin $admin)
    {
        Gate::authorize('view', $admin);

        return Inertia::render('Admins/Show', [
            'user' => AdminResource::make($admin),
            'roles' => RoleResource::collection(Role::all()),
            'occupation_types' => mapForSelect(Admin::OCCUPATION_TYPES),
            'statuses' => mapForSelect(Admin::STATUSES),
            'admins' => AdminResource::collection(Admin::all()),
        ]);
    }

    public function edit(Admin $admin)
    {
        Gate::authorize('update', $admin);

        return Inertia::render('Admins/Edit', [
            'user' => AdminResource::make($admin),
            'roles' => RoleResource::collection(Role::all()),
            'occupation_types' => mapForSelect(Admin::OCCUPATION_TYPES),
            'statuses' => mapForSelect(Admin::STATUSES),
            'admins' => AdminResource::collection(Admin::all()),
        ]);
    }

    public function update(Request $request, Admin $admin)
    {
        Gate::authorize('update', $admin);

        $validated = $request->validate($this->adminFormRules($admin, true));

        $admin->update([
            'supervisor_id' => $validated['supervisor_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'status' => $validated['status'],
            'blocked_at' => $validated['status'] == Admin::STATUS_BLOCKED ? now() : null,
            'occupation_type' => $validated['occupation_type'],
            'start_of_employment' => $validated['start_of_employment'],
            'end_of_employment' => $validated['end_of_employment'],
        ]);

        $admin->syncRoles($validated['roles'] ?? []);

        cache()->forget($admin->rolesCacheKey());

        return redirect()->back()->with('success', __('Successfully updated'));
    }

    public function destroy(Admin $admin)
    {
        Gate::authorize('delete', $admin);

        $admin->delete();

        return redirect()->route('admins.index')->with('success', __('Successfully deleted'));
    }

    public function reinvite(Request $request, Admin $admin)
    {
        Gate::authorize('update', $admin);

        $admin->notify(new AdminInvitedNotification(Password::broker('admins')->createToken($admin)));

        return back()->with('success', __('Successfully invited'));
    }

    public function restore(Admin $admin)
    {
        Gate::authorize('restore', $admin);

        $admin->restore();

        return redirect()->route('admins.index')->with('success', __('Successfully restored'));
    }

    public function unblock(Admin $admin)
    {
        Gate::authorize('update', $admin);

        $admin->update([
            'status' => Admin::STATUS_REGISTERED,
            'blocked_at' => null,
        ]);

        return redirect()->route('admins.index')->with('success', __('Successfully unblocked'));
    }

    public function forceDelete(Admin $admin)
    {
        Gate::authorize('forceDelete', $admin);

        $admin->forceDelete();

        return redirect()->route('admins.index')->with('success', __('Successfully force deleted'));
    }

    public function deletePhoto(Admin $admin)
    {
        Gate::authorize('update', $admin);

        $admin->deleteProfilePhoto();

        return back()->with('success', __('Successfully deleted the photo'));
    }
}
