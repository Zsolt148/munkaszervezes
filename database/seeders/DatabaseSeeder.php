<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Telescope\Telescope;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Telescope::stopRecording();

//        Task::factory(10)->todo()->create();
//        return;

        $this->call([
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
        ]);

        if (! app()->environment('production')) {

            //Admin::factory(15)->create();

            $this->call([
                ParkSeeder::class,
                TaskSeeder::class,
                LeaveSeeder::class,
            ]);
        }

        Telescope::startRecording();
    }
}
