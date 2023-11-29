<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Park;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $admin = Admin::query()->inRandomOrder()->first();

        return [
            'created_by' => $admin->id,
            'park_id' => Park::query()->inRandomOrder()->first()->id,
            'role_id' => $admin->roles->first()->id,
            'responsible_id' => $admin->id,
            'task_type' => $this->faker->randomKey(Task::TASK_TYPES),
            'status' => $status = $this->faker->randomKey(Task::STATUSES),
            'priority' => $this->faker->randomKey(Task::PRIORITIES),
            'name' => $this->faker->randomElement([
                'Fűnyírás',
                'Favágás',
                'Kerítésjavítás',
                'Napelemek tisztítása, lemosása',
                'Napelemek ellenőrzése',
                'Napelemek beállítása',
                'Energiatermelés ellenőrzése',
                'Hibás inverter cserélése',
                'Rutin ellenőrzés',
                'Inverterek szoftverfrissítése',
                'Kamera rendszer ellenőrzése',
                'Biztonsági rendszer ellenőrzése',
                'Tartalék áram ellenőrzése',
                'Új panelek telepítése',
            ]),
            'description' => $this->faker->text,
            'deadline' => $this->faker->dateTimeThisMonth(now()->addWeeks(3))->format('Y-m-d'),
            'date' => $this->faker->dateTimeThisMonth(),
            'estimated_hour' => $this->faker->numberBetween(1, 10),
            'done_at' => $status == 'done' ? $this->faker->dateTimeThisMonth() : null,
            'status_changed_at' => now(),
        ];
    }

    public function todo()
    {
        return $this->state(fn (array $attributes) => [
            'role_id' => null,
            'responsible_id' => null,
            'date' => null,
            'status' => Task::STATUS_TODO,
        ]);
    }
}
