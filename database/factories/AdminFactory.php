<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'occupation_type' => 'full_time',
            'password' => Hash::make('secret'),
            'remember_token' => Str::random(10),
            'start_of_employment' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Admin $admin) {
            $admin->roles()->attach(Role::query()->inRandomOrder()->first()->id);
        });
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
