<?php

namespace App\Actions\Fortify;

use App\Traits\AdminForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use AdminForm;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $admin
     * @return void
     */
    public function update($admin, array $input)
    {
        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(true),
        ])->after(function ($validator) use ($admin, $input) {
            if (! isset($input['current_password']) || ! Hash::check($input['current_password'], $admin->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        $admin->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
