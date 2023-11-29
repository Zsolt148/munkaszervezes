<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
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
        $rules = [];

        foreach (config('app.locales') as $key => $locale) {
            $rules['name.'.$key] = ['required_with:content.'.$key, 'string', 'max:255'];
            $rules['content.'.$key] = ['required_with:name.'.$key, 'string'];
        }

        return $rules;
    }
}
