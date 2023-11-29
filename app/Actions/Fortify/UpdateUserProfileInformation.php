<?php

namespace App\Actions\Fortify;

use App\Traits\AdminForm;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    use AdminForm;

    /**
     * Validate and update the given user's profile information.
     *
     * @param  mixed  $admin
     * @return void
     */
    public function update($admin, array $input)
    {
        Validator::make($input, $this->adminFormRules($admin))->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $admin->updateProfilePhoto($input['photo']);
        }

        if ($input['email'] !== $admin->email && $admin instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($admin, $input);
        } else {
            $admin->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  mixed  $admin
     * @return void
     */
    protected function updateVerifiedUser($admin, array $input)
    {
        $admin->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $admin->sendEmailVerificationNotification();
    }
}
