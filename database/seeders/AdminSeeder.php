<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function __construct()
    {
        //
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Admin::updateOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'email_verified_at' => now(),
            'password' => Hash::make('secret'),
            'occupation_type' => 'full_time',
            'start_of_employment' => now(),
        ]);

        $admin->assignRole('superadmin');
    }
}
