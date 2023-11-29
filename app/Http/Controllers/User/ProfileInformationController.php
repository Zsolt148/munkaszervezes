<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class ProfileInformationController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,
        UpdatesUserProfileInformation $updater)
    {
        $updater->update($request->user(), $request->all());

        return $request->wantsJson()
                    ? new JsonResponse('', 200)
                    : back()->with('success', __('Successfully updated'));
    }
}
