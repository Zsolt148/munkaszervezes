<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveSecuritySettingsRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => ['sometimes', 'nullable', 'required_with:password', 'string', 'min:5'],
            'password' => ['sometimes', 'nullable', 'required_with:username', 'string', 'min:5'],
        ];
    }
}
