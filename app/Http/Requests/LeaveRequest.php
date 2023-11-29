<?php

namespace App\Http\Requests;

use App\Models\Leave;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeaveRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'exists:admins,id'],
            'type' => ['required', Rule::in(array_keys(Leave::TYPES))],
            'date' => ['sometimes', 'date'],
            'dates' => ['sometimes', 'array'],
        ];
    }
}
