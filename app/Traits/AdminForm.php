<?php

namespace App\Traits;

use App\Models\Admin;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait AdminForm
{
    /**
     * @param  bool  $confirmed
     * @return string[]
     */
    public function passwordRules($confirmed = false)
    {
        $rules = [
            'required',
            'string',
            Password::defaults(), // Found in AppServiceProvider
        ];

        if ($confirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }

    /**
     * @return array|\string[][]
     */
    public function adminFormRules(?Admin $admin = null, $editByAdmin = false)
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('admins', 'email')->ignore($admin ? $admin->id : null)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];

        // Invite
        if (! $admin) {
            $rules['occupation_type'] = ['required', Rule::in(array_keys(Admin::OCCUPATION_TYPES))];
            $rules['start_of_employment'] = ['required', 'date'];
            $rules['end_of_employment'] = ['nullable', 'date'];
        }

        // Admin edit and not invite
        if ($admin && $editByAdmin) {
            $edit = [
                'status' => ['required', Rule::in(array_keys(Admin::STATUSES))],
                'occupation_type' => ['required', Rule::in(array_keys(Admin::OCCUPATION_TYPES))],
                'start_of_employment' => ['required', 'date'],
                'end_of_employment' => ['nullable', 'date'],
                'supervisor_id' => ['nullable'], //  csak a tulajdonosnak nincs, csak meghívó/pénzügyes/adminisztrátor állíthatja
                'roles' => ['nullable', 'array'],
            ];
        }

        return array_merge($rules, $edit ?? []);
    }

    public function registerRules(): array
    {
        return array_merge(
            $this->adminFormRules(),
            ['password' => $this->passwordRules(true)]
        );
    }
}
