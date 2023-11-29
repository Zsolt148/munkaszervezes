<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfilePhotoController extends Controller
{
    /**
     * Delete the current user's profile photo.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->user()->deleteProfilePhoto();

        return back(303)->with('success', __('Successfully removed'));
    }
}
