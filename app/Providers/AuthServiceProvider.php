<?php

namespace App\Providers;

use App\Models\Activity;
use App\Models\Admin;
use App\Models\Menu;
use App\Models\Settings;
use App\Models\Task;
use App\Policies\AdminPolicy;
use App\Policies\LogsPolicy;
use App\Policies\SettingsPolicy;
use App\Policies\TaskPolicy;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Admin::class => AdminPolicy::class,
        Settings::class => SettingsPolicy::class,
        Activity::class => LogsPolicy::class,
        Task::class => TaskPolicy::class,
    ];

    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(Gate $gate)
    {
        $this->registerPolicies();

        $this->registerPrivileges($gate);
    }

    /**
     * Menu View privileges mert minden modellhez policyt kell csinálni majd minden controller minden functionjébe beletenni :D
     *
     * @param  RoleBasedAccessControlService  $accessControlService
     */
    protected function registerPrivileges(Gate $gate)
    {
        $gate->before(function ($admin) {
            if ($admin instanceof Admin && $admin->hasRole('superadmin')) {
                return true;
            }
        });

        $gate->define('isSuperadmin', fn ($admin) => $admin->hasRole('superadmin'));
    }
}
