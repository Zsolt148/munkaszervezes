<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UserPasswordController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, UpdatesUserPasswords $updater)
    {
        $updater->update($request->user(), $request->all());

        return back()->with('success', __('Successfully updated'));
    }
}
