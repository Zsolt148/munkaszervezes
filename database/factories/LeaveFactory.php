<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Leave;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition(): array
    {
        return [
            'admin_id' => $this->faker->randomElement(Admin::all()->pluck('id')),
            'type' => $this->faker->randomElement(array_keys(Leave::TYPES)),
            'date' => $this->faker->dateTimeBetween('-10 days', now()->addDays(7))->format('Y-m-d'),
            'accepted_at' => now(),
        ];
    }
}
