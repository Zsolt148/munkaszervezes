<?php

namespace Database\Seeders;

use App\Helpers\AdminFeatures;
use App\Helpers\PermissionRegistrar;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // spatie laravel permissions
        // https://spatie.be/docs/laravel-permission/v5/introduction

        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
        cache()->clear();

        // create permissions
        // permission policies as default laravel policy - policy name (controller method)
        // viewAny (index), view (show), create (create,store), update (edit,update), delete (destroy)

        $this->defaultPermissions();
        $this->permissions();
        $this->roles();
    }

    private function defaultPermissions(): void
    {
        // Admins CRUD
        Permission::findOrCreate('view_admins');
        Permission::findOrCreate('create_admins');
        Permission::findOrCreate('update_admins');
        Permission::findOrCreate('delete_admins');

        // Logs
        if (AdminFeatures::hasLogs()) {
            Permission::findOrCreate('view_logs');
        }

        // Settings
        Permission::findOrCreate('update_settings');
    }

    private function permissions(): void
    {
        // resource planning
        Permission::findOrCreate('resource_planning');

        // tasks
        Permission::findOrCreate('tasks');
    }

    private function roles(): void
    {
        // Superadmin - god
        $role = Role::findOrCreate('superadmin');
        $role->syncPermissions(Permission::all());

        // Tulajdonos
        $owner = Role::findOrCreate('owner');
        $owner->syncPermissions(Permission::all());

        // Cégvezető
        $company = Role::findOrCreate('company_manager', $owner->id);
        $company->syncPermissions(Permission::all());

        $this->it($company); // IT
        $this->technicals($company); // Műszaki
    }

    private function it(Role $company): void
    {
        // IT Vezető
        $it = Role::findOrCreate('it_manager', $company->id);

        // IT munkatárs
        $employee = Role::findOrCreate('it_employee', $it->id);
    }

    private function technicals(Role $company): void
    {
        // Műszaki Vezető
        $tech = Role::findOrCreate('technical_leader', $company->id);
        $tech->syncPermissions(['tasks', 'resource_planning']);

        // Műszaki ügyintéző
        $technical_administrator = Role::findOrCreate('technical_administrator', $tech->id);
        $technical_administrator->syncPermissions(['tasks']);

        // Karbantartási Vezető
        $maintenance = Role::findOrCreate('maintenance_manager', $tech->id);
        $maintenance->syncPermissions(['tasks', 'resource_planning']);

        // Karbantartási csoportvezető
        $maintenance_team_leader = Role::findOrCreate('maintenance_team_leader', $maintenance->id);
        $maintenance_team_leader->syncPermissions(['tasks', 'resource_planning']);

        // Karbantartó
        $maintenance_employee = Role::findOrCreate('maintenance_employee', $maintenance_team_leader->id);
        $maintenance_employee->syncPermissions(['tasks']);

        // Villamossági Vezető
        $electricity = Role::findOrCreate('electricity_manager', $tech->id);
        $electricity->syncPermissions(['tasks', 'resource_planning']);

        // Villamossági csoportvezető
        $electricity_team_leader = Role::findOrCreate('electricity_team_leader', $electricity->id);
        $electricity_team_leader->syncPermissions(['tasks', 'resource_planning']);

        // Villanyszerelő
        $electrician = Role::findOrCreate('electricity_employee', $electricity_team_leader->id);
        $electrician->syncPermissions(['tasks']);
    }
}
