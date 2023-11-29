<?php

namespace App\Actions\Fortify;

use App\Models\Admin;
use App\Traits\AdminForm;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use AdminForm;

    /**
     * Validate and create a newly registered user.
     *
     * @return \App\Models\Admin
     */
    public function create(array $input)
    {
        Validator::make($input, $this->registerRules())
            ->validate();

        return Admin::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
