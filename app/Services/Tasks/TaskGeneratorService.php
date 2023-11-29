<?php

namespace App\Services\Tasks;

use App\Models\Admin;
use App\Models\Task;

class TaskGeneratorService
{
    protected Admin $admin;

    protected int $count = 1;

    protected string $name = '';

    protected array $properties = [];

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }

    public static function make(Admin $admin): self
    {
        return new self($admin);
    }

    public static function defaultTasks(): array
    {
        return [
            0 => [
                'count' => 1,
                'properties' => [
                    'name' => [
                        'label' => 'Name',
                        'type' => 'text',
                        'value' => 'Fűnyírás',
                        'required' => true,
                    ],
                    'task_type' => [
                        'label' => 'Task type',
                        'type' => 'select',
                        'value' => 'task',
                        'items' => mapForSelect(Task::TASK_TYPES),
                        'required' => true,
                    ],
                    'status' => [
                        'label' => 'Status',
                        'type' => 'select',
                        'value' => Task::STATUS_TODO,
                        'items' => mapForSelect(Task::STATUSES),
                        'required' => true,
                    ],
                    'priority' => [
                        'label' => 'Priority',
                        'type' => 'select',
                        'value' => 'medium',
                        'items' => mapForSelect(Task::PRIORITIES),
                        'required' => true,
                    ],
                    'description' => [
                        'label' => 'Description',
                        'type' => 'text',
                        'value' => '',
                        'required' => false,
                    ],
                    'deadline' => [
                        'label' => 'Deadline',
                        'type' => 'date',
                        'value' => now()->addMonth()->format('Y-m-d'),
                        'required' => true,
                    ],
                    'date' => [
                        'label' => 'Date',
                        'type' => 'date',
                        'value' => now()->addMonth()->format('Y-m-d'),
                        'required' => true,
                    ],
                    'estimated_hour' => [
                        'label' => 'Estimated time',
                        'type' => 'number',
                        'value' => 1,
                        'required' => true,
                    ],
                    'travel_time' => [
                        'label' => 'Travel time',
                        'type' => 'number',
                        'value' => 1,
                        'required' => true,
                    ],
                ],
            ],
        ];
    }

    public static function emptyTask(): array
    {
        return [
            'count' => 0,
            'properties' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'value' => '-',
                    'required' => true,
                ],
                'task_type' => [
                    'label' => 'Task type',
                    'type' => 'select',
                    'value' => 'task',
                    'items' => mapForSelect(Task::TASK_TYPES),
                    'required' => true,
                ],
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'value' => Task::STATUS_TODO,
                    'items' => mapForSelect(Task::STATUSES),
                    'required' => true,
                ],
                'priority' => [
                    'label' => 'Priority',
                    'type' => 'select',
                    'value' => 'medium',
                    'items' => mapForSelect(Task::PRIORITIES),
                    'required' => true,
                ],
                'description' => [
                    'label' => 'Description',
                    'type' => 'text',
                    'value' => '',
                    'required' => false,
                ],
                'deadline' => [
                    'label' => 'Deadline',
                    'type' => 'date',
                    'value' => now()->addMonth()->format('Y-m-d'),
                    'required' => true,
                ],
                'date' => [
                    'label' => 'Date',
                    'type' => 'date',
                    'value' => now()->addMonth()->format('Y-m-d'),
                    'required' => true,
                ],
                'estimated_hour' => [
                    'label' => 'Estimated time',
                    'type' => 'number',
                    'value' => 1,
                    'required' => true,
                ],
                'travel_time' => [
                    'label' => 'Travel time',
                    'type' => 'number',
                    'value' => 1,
                    'required' => true,
                ],
            ],
        ];
    }

    public static function mapProperties(array $properties): array
    {
        return collect($properties)
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item['value']];    // key value pairs for $model->fill()
            })
            ->toArray();
    }

    public function name(string $name = ''): self
    {
        $this->name = $name;

        return $this;
    }

    public function count(int $count = 1): self
    {
        $this->count = $count;

        return $this;
    }

    public function properties(array $properties = []): self
    {
        $this->properties = $properties;

        return $this;
    }

    public function create(): array
    {
        $tasks = [];

        for ($i = 0; $i < $this->count; $i++) {
            $tasks[] = $this->createTask();
        }

        return $tasks;
    }

    private function createTask(): Task
    {
        $task = new Task();

        $task->createdBy()->associate($this->admin);

        $task->task_type = 'task';
        $task->status = Task::STATUS_TODO;
        $task->priority = 'medium';

        $task->name = $this->name;
        $task->estimated_hour = 1;
        $task->status_changed_at = now();

        if (! empty($this->properties)) {
            $task->fill($this->properties);
        }

        $task->save();

        return $task;
    }
}
