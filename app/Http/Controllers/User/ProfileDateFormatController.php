<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProfileDateFormatController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Laravel\Fortify\Contracts\UpdatesUserProfileInformation  $updater
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $request->validate([
            'date_time_format' => ['required', Rule::in(Admin::DATE_TIME_FORMATS)],
        ]);

        $request->user()->update([
            'date_time_format' => $request->date_time_format,
        ]);

        return back()->with('success', __('Successfully updated'));
    }
}
