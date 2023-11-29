<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $roleIds = Role::pluck('id')->toArray();

        return [
            'role_id' => ['nullable', 'exists:roles,id'],
            'park_id' => ['required', 'exists:parks,id'],
            'responsible_id' => Rule::requiredIf(fn () => in_array($this->role, $roleIds)),
            'subtasks' => ['nullable', 'array'],
            'task_type' => ['required', Rule::in(array_keys(Task::TASK_TYPES))],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'name' => ['required', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'description' => ['nullable', 'string'],
            'deadline' => ['required', 'date'],
            'date' => ['required', 'date'],
            'travel_time' => ['nullable', 'numeric', 'max:999.99'],
            'estimated_hour' => ['required', 'integer'],
            'files' => ['nullable', 'array'],
        ];
    }
}
