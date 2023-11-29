<?php

namespace Database\Seeders;

use App\Models\Park;
use Illuminate\Database\Seeder;

class ParkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Park::query()
            ->firstOrCreate(['name' => 'Park #1', 'zip' => 6000, 'city' => 'Kecskemét', 'address' => 'Izsáki út 10'])
            ->firstOrCreate(['name' => 'Park #2', 'zip' => 6000, 'city' => 'Kecskemét', 'address' => 'Izsáki út 10'])
            ->firstOrCreate(['name' => 'Park #3', 'zip' => 6000, 'city' => 'Kecskemét', 'address' => 'Izsáki út 10'])
            ->firstOrCreate(['name' => 'Park #4', 'zip' => 6000, 'city' => 'Kecskemét', 'address' => 'Izsáki út 10'])
            ->firstOrCreate(['name' => 'Park #5', 'zip' => 6000, 'city' => 'Kecskemét', 'address' => 'Izsáki út 10']);
    }
}
