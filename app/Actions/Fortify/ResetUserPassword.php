<?php

namespace App\Actions\Fortify;

use App\Traits\AdminForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use AdminForm;

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  mixed  $admin
     * @return void
     */
    public function reset($admin, array $input)
    {
        Validator::make($input, [
            'password' => $this->passwordRules(true),
        ])->validate();

        $admin->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
